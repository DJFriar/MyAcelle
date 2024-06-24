<div class="topfix-header">
    <div class="topfix-container border-bottom">
        <div class="py-2 d-flex align-items-center" style="height:55px;">
            <div class="d-flex align-items-center" style="width:calc(100% - 200px);">
                <div class="">
                    <a href="javascript:;" page-action="activity" class="topbar-activity-history text-secondary" style="margin-top:2px;font-size:20px">
                        <span class="material-symbols-rounded">
                            history
                        </span>
                    </a>
                    <script>
                        $(function() {
                            sidebar = new Sidebar();
                            $('[page-action="activity"]').on('click', function() {
                                if(!sidebar.showed()) {
                                    sidebar.load({
                                        url: '{{ action('AccountController@activity') }}'
                                    });
                                } else {
                                    sidebar.hide();
                                }
                            });
                        });
                    </script>
                </div>
                <div class="text-muted2 small how-use hide">
                    <a href="javascript:;" class="text-secondary">
                        <span class="material-symbols-rounded">help</span>
                        How to use
                    </a>
                </div>
                <div class="topfix-search text-center d-flex justify-content-center" style="width:100%">
                    <div class="topfix-search-icon">
                        <input page-action="top-search-input" type="text" name="keyword" class="topfix-search-input form-control"
                            placeholder="Search" />
                        <span class="topfix-search-icon-span material-symbols-rounded">search</span>
                        <span class="topfix-close-icon-span material-symbols-rounded" style="display: none">close</span>
                    </div>
                </div>
            </div>
        </div>
        

        <script>
            $(function() {
                $('[page-action="top-search-input"]').on('click', function() {
                    TopSearchBar.openSearch();
                    TopSearchBar.search();
                });
                
            });
        </script>
    </div>
</div>