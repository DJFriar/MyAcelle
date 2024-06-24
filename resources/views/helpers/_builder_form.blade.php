<script>
    // popup banner element
    class PopupBannerElement extends SuperElement {
        name() {
            return getI18n('image');
        }
        icon() {
            return 'fal fa-image';
        }

        getControls() {
            var element = this;
            element.getWidth = function() {
                var width = element.obj.css('background-size').split(' ')[0].replace('%','');
                if (width.includes('px')) {
                    width = (width.replace('px','')/element.obj.width())*100;
                } else if (width == 'auto') {
                    width = '100';
                }

                return width;
            };
            element.getHeight = function() {
                var height = element.obj.css('background-size').split(' ').length > 1 ? element.obj.css('background-size').split(' ')[1] : 'auto';
                var autoHeight = false;
                if (height.includes('%')) {
                    height = height.replace('%','')*(height.replace('%','')/100);
                } else if (height == 'auto') {
                    autoHeight = true;
                }

                return height;
            };
            element.getAutoHeight = function() {
                var height = element.obj.css('background-size').split(' ').length > 1 ? element.obj.css('background-size').split(' ')[1] : 'auto';
                var autoHeight = false;
                if (height == 'auto') {
                    autoHeight = true;
                }

                return autoHeight;
            };
            
            return [
                new ImageControl(getI18n('align'), {
                    width: element.getWidth(),
                    height: element.getHeight(),
                    autoHeight: element.getAutoHeight(),
                    src: element.obj.css('background-image').match(/url\((\"|\')?([^(\"|\')]+)/)[2],
                    alt: '',
                    align: element.obj.css('background-position').split(' ')[0],
                    widthRange: 500
                }, {
                    setWidth: function(width) {
                        element.obj.css('background-size', width + ' ' + element.getHeight());
                        currentEditor.select(element);
                        currentEditor.handleSelect();
                    },
                    setHeight: function(height) {
                        element.obj.css('background-size', element.getWidth() + '% ' + height);
                        currentEditor.select(element);
                        currentEditor.handleSelect();
                    },
                    setRange: function(range) {
                        element.obj.css('background-size', 'auto ' + range);
                        currentEditor.select(element);
                        currentEditor.handleSelect();
                    },
                    setUrl: function(url) {
                        element.obj.css('background-image', 'url("'+url+'")');
                        element.obj.addClass('bg-changed');
                        currentEditor.select(element);
                        currentEditor.handleSelect();
                    },
                    setAlign: function(align) {
                        element.obj.css('background-position', align + ' center');
                        currentEditor.select(element);
                        currentEditor.handleSelect();
                    },
                    setAlt: function(alt) {
                        
                    }
                }),
                new BackgroundImageControl(getI18n('background_image'), {
                    image: element.obj.css('background-image'),
                    color: element.obj.css('background-color'),
                    repeat: element.obj.css('background-repeat'),
                    position: element.obj.css('background-position'),
                    size: element.obj.css('background-size'),
                }, {
                    setBackgroundImage: function (image) {
                        element.obj.css('background-image', image);
                        currentEditor.select(element);
                        currentEditor.handleSelect();
                    },
                    setBackgroundColor: function (color) {
                        element.obj.css('background-color', color);
                    },
                    setBackgroundRepeat: function (repeat) {
                        element.obj.css('background-repeat', repeat);
                    },
                    setBackgroundPosition: function (position) {
                        element.obj.css('background-position', position);
                        currentEditor.select(element);
                        currentEditor.handleSelect();
                    },
                    setBackgroundSize: function (size) {
                        element.obj.css('background-size', size);
                        currentEditor.select(element);
                        currentEditor.handleSelect();
                    },
                })
            ];
        }
    }
</script>