import $ from 'jquery';

class Search {

    // 1. describe and create our object
    constructor() {
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
        this.typingTimer = setTimeout(this.getResults.bind(this), 2000);
        this.previousValue = this.searchField.val();
    }

    getResults() {
        this.isSpinnervisible = false;
        this.resultsDiv.html("Imagine real search results here");
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
}

export default Search;