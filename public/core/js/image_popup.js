class ImagePopup {
    constructor(element) {
        this.element = element;

        this.popup = new Popup({
            backdrop: true,
        });

        // events
        this.events();
    }

    getUrl() {
        return this.element.getAttribute('href');
    }

    getImage() {
        return this.popup.popup.find('[image-popup]');
    }

    mask() {
        this.popup.popup.find('.modal-body').append(`
            <div class="image-loading py-5 text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            <div>
        `);
        this.getImage().hide();
    }

    unmask() {
        this.popup.popup.find('.image-loading').remove();
        this.getImage().show();
    }

    events() {
        var _this = this;

        this.element.addEventListener('click', (e) => {
            e.preventDefault();
            
            this.popup.loadHtml(`
                <div class="modal-dialog shadow">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-3">
                            <img image-popup width="100%" src="`+this.getUrl()+`" />
                        </div>
                    </div>
                </div>
            `);

            //
            this.mask();
            this.getImage()[0].onload = function () {
                _this.unmask();
            }
        });
    }
}