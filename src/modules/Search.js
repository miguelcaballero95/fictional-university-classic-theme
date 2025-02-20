import $ from 'jquery';

class Search {

    // 1. describe and create our object
    constructor() {
        this.addSearchHTML();
        this.openButton = $(".js-search-trigger");
        this.closeButton = $(".search-overlay__close");
        this.searchOverlay = $(".search-overlay");
        this.searchField = $("#search-term");
        this.resultsDiv = $("#search-overlay__results");
        this.previousValue;
        this.isOverlayOpen = false;
        this.isSpinnervisible = false;
        this.typingTimer;
        this.events();
    }

    // 2. events
    events() {
        this.openButton.on('click', this.openOverlay.bind(this));
        this.closeButton.on("click", this.closeOverlay.bind(this));
        $(document).on('keydown', this.keyPressDispatcher.bind(this));
        this.searchField.on("keyup", this.typingLogic.bind(this));
    }

    // 3. methods

    typingLogic() {

        if (this.searchField.val() == this.previousValue)
            return;

        clearTimeout(this.typingTimer);

        if (!this.searchField.val()) {
            this.resultsDiv.html('');
            this.isSpinnervisible = false;
            return
        }

        if (!this.isSpinnervisible) {
            this.resultsDiv.html('<div class="spinner-loader"></div>')
            this.isSpinnervisible = true;
        }
        this.typingTimer = setTimeout(this.getResults.bind(this), 750);
        this.previousValue = this.searchField.val();
    }

    /**
     * This method makes requests to WordPress REST API and popoulate the results div
     */
    getResults() {
        $.getJSON(`${universityData.root_url}/wp-json/university/v1/search?term=${this.searchField.val()}`, (results) => {
            this.resultsDiv.html(`
                <div class="row">
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">General Information</h2>
                        ${results.general_info.length
                    ? `<ul class="link-list min-list">
                            ${results.general_info.map(item => `<li><a href="${item.permalink}">${item.title}</a>${item.post_type == 'post' ? ` by ${item.author_name}` : ''}</li>`).join('')}
                        </ul>`
                    : `<p>No general information matches that search</p>`}
                    </div>
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Programs</h2>
                        ${results.programs.length
                    ? `<ul class="link-list min-list">
                            ${results.programs.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join('')}
                        </ul>`
                    : `<p>No programs matches that search. <a href="${universityData.root_url}/programs">View all programs</a></p>`}
                        <h2 class="search-overlay__section-title">Professors</h2>
                        ${results.professors.length
                    ? `<ul class="professor-cards">
                            ${results.professors.map(item => `
                            <li class="professor-card__list-item">
                                <a class="professor-card" href="${item.permalink}">
                                    <img class="professor-card__image" src="${item.thumbnail}" alt="">
                                    <span class="professor-card__name">${item.title}</span>
                                </a>
                            </li>
                            `).join('')}
                        </ul>`
                    : `<p>No professors matches that search.</p>`}
                    </div>
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Campuses</h2>
                        ${results.campuses.length
                    ? `<ul class="link-list min-list">
                            ${results.campuses.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join('')}
                        </ul>`
                    : `<p>No campuses matches that search <a href="${universityData.root_url}/campuses">View all campuses</a></p>`}
                        <h2 class="search-overlay__section-title">Events</h2>
                        ${results.events.length
                    ? `${results.events.map(item => `
                    <div class="event-summary">
                        <a class="event-summary__date t-center" href="${item.permalink}">
                            <span class="event-summary__month">${item.month}</span>
                            <span class="event-summary__day">${item.day}</span>
                        </a>
                        <div class="event-summary__content">
                            <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}">${item.title}</a></h5>
                            <p>${item.description} <a href="${item.permalink}" class="nu gray">Learn more</a></p>
                        </div>
                    </div>`).join('')}`
                    : `<p>No events matches that search <a href="${universityData.root_url}/events">View all events</a></p>`}
                    </div>
                </div>
            `);
            this.isSpinnervisible = false;
        });
    }

    /**
     * This method is listening for S and ESC keys to open or close the overlay
     * @param {Event} e 
     */
    keyPressDispatcher(e) {
        // 83 is equal to S key and 27 is equal to ESC key
        if (e.keyCode == 83 && !this.isOverlayOpen && !$("input, textarea").is(':focus'))
            this.openOverlay();
        else if (e.keyCode == 27 && this.isOverlayOpen)
            this.closeOverlay();
    }

    /**
     * Open the search overlay
     */
    openOverlay() {
        this.searchOverlay.addClass("search-overlay--active");
        $("body").addClass("body-no-scroll");
        this.searchField.val('');
        this.resultsDiv.html('');
        setTimeout(() => this.searchField.focus(), 301);
        this.isOverlayOpen = true;
        return false;
    }

    /**
     * Close the searh overlay
     */
    closeOverlay() {
        this.searchOverlay.removeClass("search-overlay--active");
        $("body").removeClass("body-no-scroll");
        this.isOverlayOpen = false;
    }

    /**
     * Adding the search overlay at the end of the body
     */
    addSearchHTML() {
        $("body").append(`
            <div class="search-overlay">
                <div class="search-overlay__top">
                    <div class="container">
                        <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
                        <input type="text" name="" placeholder="What are you looking for?" id="search-term" class="search-term" autocomplete="off">
                        <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="container">
                    <div id="search-overlay__results"></div>
                </div>
            </div>`);
    }
}

export default Search;