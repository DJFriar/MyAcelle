<script>
    // rss element
    class RssElement extends SuperElement  {
        name() {
            var element = this; 
            return getI18n('block');
        }

        unselectCallback() {
            this.obj.find('*').removeClass('rss-selected');
        }

        getOptions() {
            var _this = this;

            return {
                preview: this.obj.attr('data-preview'),
                config: JSON.parse(Base64.decode(this.obj.attr('data-config'))),
                getSelectedItem: function() {
                    return _this.selectedItem;
                }                
            };
        }

        setOptions(options, callback) {
            var _this = this;

            if (typeof(options.preview) != 'undefined') {
                this.obj.attr('data-preview', options.preview);
            }
            if (typeof(options.config) != 'undefined') {
                this.obj.attr('data-config', Base64.encode(JSON.stringify($.extend({}, _this.getOptions().config, options.config))));
            }

            _this.render(callback);

            currentEditor.handleSelect();
        }

        addEvents() {
            var element = this;
            window.cElement = this;

            // preview
            element.obj.find('.ace-preview').unbind('click');
            element.obj.find('.ace-preview').on('click', function() {
                element.setOptions({
                    preview: 'yes'
                });
            });

            // preview
            element.obj.find('.ace-unpreview').unbind('click');
            element.obj.find('.ace-unpreview').on('mouseup', function() {
                element.setOptions({
                    preview: 'no'
                });
            });

            // hover rss items
            element.obj.find('[rss-item]').unbind('mouseenter');
            element.obj.find('[rss-item]').unbind('mouseleave');
            element.obj.find('[rss-item]').mouseenter( function() {
                var rssItem = element.rssItemFactory($(this));
                rssItem.highlight();
            } ).mouseleave( function() {
                var rssItem = element.rssItemFactory($(this));
                rssItem.removeHighlight();
            } );

            // click outside item
            element.obj.find('[rss-item]').unbind('click');
            element.obj.find('[rss-item]').on('click', function(e) {
                var item = $(this);

                
                if(typeof(currentEditor.selected.rssItemFactory) !== 'undefined') {
                    var rssItem = element.rssItemFactory(item);
                    element.selectItem(rssItem);
                // rss element not selected then select
                } else {
                    var t = $(this).closest('[builder-element="RssElement"]');
                    var e = currentEditor.elementFactory(t);
                    currentEditor.select(e);
                    currentEditor.handleSelect(function() {
                        setTimeout(function() {
                            var rssItem = e.rssItemFactory(item);
                            e.selectItem(rssItem);
                        },200);
                    });
                }
            } )

            // click no item
            element.obj.on('click', function(e) {
                if(!$(e.target).closest('[rss-item]').length) {
                    element.unselectItem();
                }
            } )

            // set before save event
            element.setBeforeSaveEvent();
        }

        selectItem(rssItem) {
            console.log('unselect');
            this.unselectItem();

            //
            console.log('select');
            this.selectedItem = rssItem;            
            this.selectedItem.select();

            // reload controls
            currentEditor.handleSelect();
        }

        unselectItem() {
            if (typeof(this.selectedItem) == 'undefined' || this.selectedItem == null) {
                return;
            }

            this.selectedItem.unselect();
            this.selectedItem = null;

            currentEditor.handleSelect();
        }

        rssItemFactory(item) {
            return new RssItem(this, item);
        }

        setBeforeSaveEvent() {
            var element = this;
        }

        render(callback) {
            var _this = this;

            if (_this.getOptions().preview == 'no') {
                _this.loadPlaceholder();
            } else if (_this.getOptions().preview == 'yes') {
                _this.loadRss(callback);
            }
        }

        setContent(content) {
            var button;
            if (this.getOptions().preview == 'no') {
                button = `
                    <button class="btn btn-secondary ace-rss-button ace-preview shadown-sm" style="display:none">{{ trans('messages.rss.preview') }}</button>
                `;
            } else {
                button = `
                    <button class="btn btn-secondary ace-rss-button ace-unpreview shadown-sm" style="display:none">{{ trans('messages.rss.unpreview') }}</button>
                `;
            }

            var html =  `
                <div style="position:relative">
                    `+content+`
                    `+button+`
                </div>
            `;

            this.obj.html(html);

            this.addEvents();
        }

        loadPlaceholder() {
            var _this = this;

            _this.setContent(`
                <div f-role="placeholder" class="rss-placeholder" style="text-align: center; display: flex; background-color: #666; padding: 40px 50px; color: #fff; border-radius: 5px;align-items:center;">
                    <div style="margin-right: 50px;">
                        <svg width="100px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 492 492"><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><circle cx="73.73" cy="418.3" r="69.7" style="fill:#fa9826"/><path d="M0,162.66v93C136,255.63,236.38,356,236.38,492h93C329.34,308,184,162.66,0,162.66Z" style="fill:#ef8114"/><path d="M8,0V92.94C224,92.94,399.06,268,399.06,492H492C492,220,272,0,8,0Z" style="fill:#ef8114"/><circle cx="73.73" cy="418.3" r="69.7" style="fill:#fa9826"/><path d="M0,162.66v93c120,0,221.8,90,234.67,206A469.17,469.17,0,0,0,322.92,426C294.17,275.78,160,162.66,0,162.66Z" style="fill:#fa9826"/><path d="M456.17,306.85C383.62,126.62,208,0,8,0V92.94c176,0,331.84,122.31,376.88,290.93C414.8,359.66,439.89,337.16,456.17,306.85Z" style="fill:#fa9826"/><path d="M73.73,348.58A69.66,69.66,0,0,0,30.94,473.25c37.74-13.59,75.72-33,112.35-58A69.67,69.67,0,0,0,73.73,348.58Z" style="fill:#fa9826"/><path d="M0,162.66v93c88,0,161.7,45.19,202.41,112.62,7.92-7.08,15.41-14.4,23.07-22C240.66,331,254.29,314.1,267.26,298,208.18,215.43,112,162.66,0,162.66Z" style="fill:#fa9826"/><path d="M8,0V92.94c120,0,232.25,57.19,304.75,146.48,18.19-29.88,33.06-57.24,43.82-87.6C267.8,60.24,144,0,8,0Z" style="fill:#fa9826"/><path d="M106.51,356.78a69.64,69.64,0,0,0-92.13,98C49.63,431.7,81,398.18,106.51,356.78Z" style="fill:#f9bd28"/><path d="M163.34,205C115.8,177.74,64,162.66,0,162.66v93c54.87.5,96,16.86,134.24,41.46A511.12,511.12,0,0,0,163.34,205Z" style="fill:#f9bd28"/><path d="M8,92.94c56,0,117.5,14.64,169.4,39.76,1.13-14.74.84-30.22.84-45.4,0-19.19.53-38-1.26-56.5C123.89,10.93,64,0,8,0Z" style="fill:#f9bd28"/></g></g></svg>
                    </div>
                    <div style="text-align: left;">
                        <h4>RSS Feeds</h4>
                        <p style="margin-bottom:0;font-size:14px">RSS stands for Really Simple Syndication, and it offers an easy way to<br />
                        stay up to date on new content from websites you care about<br />
                        Click to set up your RSS content</p>
                    </div>
                </div>
            `);

            // _this.obj.find('img')[0].onload = function() {
            //     currentEditor.select(_this);
            //     currentEditor.handleSelect();
            // };
        }

        loadRss(callback) {
            var _this = this;
            var options = _this.getOptions();

            if (options.config.url == '') {
                alert('{{ trans('messages.rss.url_required') }}');
                return;
            }

            // 
            _this.addLoadingEffect();
            $.ajax({
                url: '{!! action('TemplateController@parseRss') !!}',
                method: 'GET',
                data: options,
                statusCode: {
                    // validate error
                    400: function (res) {
                        console.log('Something went wrong!');
                    }
                },
                success: function (response) {
                    _this.setContent(response);
                    _this.removeLoadingEffect();

                    currentEditor.select(_this);

                    if (typeof(callback) != 'undefined') {
                        callback();
                    }
                }
            });
        }

        addLoadingEffect() {
            var _this = this;

            this.removeLoadingEffect();

            _this.obj.addClass('ace-loading');
            _this.obj.append(`<div class="ace-loader"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background-image: none; display: block; shape-rendering: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                <circle cx="30" cy="50" fill="#774023" r="20">
                <animate attributeName="cx" repeatCount="indefinite" dur="1s" keyTimes="0;0.5;1" values="30;70;30" begin="-0.5s"/>
                </circle>
                <circle cx="70" cy="50" fill="#d88c51" r="20">
                <animate attributeName="cx" repeatCount="indefinite" dur="1s" keyTimes="0;0.5;1" values="30;70;30" begin="0s"/>
                </circle>
                <circle cx="30" cy="50" fill="#774023" r="20">
                <animate attributeName="cx" repeatCount="indefinite" dur="1s" keyTimes="0;0.5;1" values="30;70;30" begin="-0.5s"/>
                <animate attributeName="fill-opacity" values="0;0;1;1" calcMode="discrete" keyTimes="0;0.499;0.5;1" dur="1s" repeatCount="indefinite"/>
                </circle>
                <!-- [ldio] generated by https://loading.io/ --></svg></div>
            `);
        }

        removeLoadingEffect() {
            var _this = this;
            _this.obj.removeClass('ace-loading');
            _this.obj.find('.ace-loader').remove();
        }

        updateTemplate(key, options, callback) {
            var _this = this;

            var templates = _this.getOptions().config.templates;
            templates[key] = $.extend({}, templates[key], options);

            _this.setOptions({
                config: {
                    templates: templates
                }
            }, callback);
        }

        selectItemByClass(name) {
            var element = this;

            if (element.obj.find('[rss-item="'+name+'"]').length) {
                var item = element.rssItemFactory(element.obj.find('[rss-item="'+name+'"]'));
                element.selectItem(item);
            }
        }

        getControls() {
            var element = this;

            window.testE = this;

            element.addEvents();

            return [
                new RssControl(getI18n('font_family'), element.getOptions(), {
                    setOptions: function(options) {
                        element.setOptions(options);
                    },
                    saveItemShow: function(key, show) {
                        element.updateTemplate(key, {
                            show: show
                        });
                    },
                    selectItemByClass: function(name) {
                        element.selectItemByClass(name);
                    },
                    unselectItem: function() {
                        element.unselectItem();
                    },
                    saveItemTemplate: function(key, content) {
                        element.updateTemplate(key, {
                            template: content
                        }, function() {
                            element.selectItemByClass(key);
                        });
                    }
                }),
                new FontFamilyControl(getI18n('font_family'), element.obj.css('font-family'), function(font_family) {
                    element.obj.css('font-family', font_family);
                    element.select();
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
                    },
                    setBackgroundColor: function (color) {
                        element.obj.css('background-color', color);
                    },
                    setBackgroundRepeat: function (repeat) {
                        element.obj.css('background-repeat', repeat);
                    },
                    setBackgroundPosition: function (position) {
                        element.obj.css('background-position', position);
                    },
                    setBackgroundSize: function (size) {
                        element.obj.css('background-size', size);
                    },
                }),
                new BlockOptionControl(getI18n('block_options'), { padding: element.obj.css('padding'), top: element.obj.css('padding-top'), bottom: element.obj.css('padding-bottom'), right: element.obj.css('padding-right'), left: element.obj.css('padding-left') }, function(options) {
                    element.obj.css('padding', options.padding);
                    element.obj.css('padding-top', options.top);
                    element.obj.css('padding-bottom', options.bottom);
                    element.obj.css('padding-right', options.right);
                    element.obj.css('padding-left', options.left);
                    element.select();
                })
            ];
        }
    }
</script>