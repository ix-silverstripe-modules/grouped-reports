<div class="cms-content-tools fill-height cms-panel west" data-expandOnClick="true" data-layout-type="border" id="cms-content-tools-CMSMain" style="z-index:10">
    <div class="panel panel--scrollable flexbox-area-grow fill-height cms-panel-content panel--padded">

        <div id="treepanes">
            <div id="reporttree_holder">
                <% if $GroupedReports %>
                    <dl id="" class="tree unformatted">
                        <% loop $GroupedReports.GroupedBy('Grouping').sort('Grouping ASC') %>
                            <dl>
                                <dt>$Grouping</dt>
                                <% loop $Children.sort('Title') %>
                                    <dd class="">
                                        <a href="$Link" title="$description.Att">$TreeTitle</a>
                                    </dd>
                                <% end_loop %>
                            </dl>
                        <% end_loop %>
                    </dl>
                <% end_if %>
            </div>
        </div>

    </div>
    <div class="cms-panel-content-collapsed">
        <h3 class="cms-panel-header">Reports</h3>
    </div>
    <div class="toolbar toolbar--south cms-panel-toggle">
        <a class="toggle-expand" href="#"><span>&raquo;</span></a>
        <a class="toggle-collapse" href="#"><span>&laquo;</span></a>
    </div>
</div>
