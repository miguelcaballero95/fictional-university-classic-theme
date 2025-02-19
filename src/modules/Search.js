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
        $.when(
            $.getJSON(`${universityData.root_url}/wp-json/wp/v2/posts?search=${this.searchField.val()}`),
            $.getJSON(`${universityData.root_url}/wp-json/wp/v2/pages?search=${this.searchField.val()}`)
        ).then((posts, pages) => {
            const combinedResults = posts[0].concat(pages[0]);
            this.resultsDiv.html(`
                <h2 class="search-overlay__section-title">General Information</h2>
                ${combinedResults.length ? `
                    <ul class="link-list min-list">
                        ${combinedResults.map(item => `<li><a href="${item.link}">${item.title.rendered}</a>${item.type == 'post' ? ` by ${item.author_name}` : ''}</li>`).join('')}
                    </ul >`
                    : `<p>No general information matches that search</p>`
                }
            `);
            this.isSpinnervisible = false;
        }, () => {
            this.resultsDiv.html('<p>Unexpected error; please try again.</p>');
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