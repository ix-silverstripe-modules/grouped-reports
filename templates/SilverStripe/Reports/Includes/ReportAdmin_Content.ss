<div id="reportadmin-cms-content" class="has-panel cms-content flexbox-area-grow fill-height $BaseCSSClasses"
     data-layout-type="border" data-pjax-fragment="Content" data-ignore-tab-state="true">
    <div class="toolbar toolbar--north cms-content-header vertical-align-items">
        <% with $EditForm %>
            <div class="cms-content-header-info flexbox-area-grow vertical-align-items">
                <% include SilverStripe\\Admin\\BackLink_Button %>
                <% with $Controller %>
                    <% include SilverStripe\\Admin\\CMSBreadcrumbs %>
                <% end_with %>
            </div>
        <% end_with %>
    </div>
    <div class="fill-width flexbox-area-grow fill-height">
        <% if $EditForm.fields.first.title %>
            $Tools
        <% end_if %>

        <% if $EditForm.fields.first.title %>

            <div class="flexbox-area-grow fill-height" data-layout-type="border">
                $EditForm
            </div>

        <% else %>

            <div class="fill-height flexbox-area-grow panel panel--scrollable cms-panel-content panel--padded">

                <div id="treepanes">
                    <div id="reporttree_holder">
                        <% if $GroupedReports %>
                            <dl id="" class="tree unformatted">
                                <% loop $GroupedReports.GroupedBy('Grouping').sort('Grouping ASC') %>
                                    <% if $Grouping %>
                                    <dl>
                                        <dt class="font-icon-chart-line"> $Grouping</dt>
                                        <% loop $Children.sort('Title') %>
                                            <dd class="">
                                                <a href="$Link" title="$description.Att"><span
                                                        class="report-title">$TreeTitle</span> </a><% if $description %>
                                                <br/>
                                                <span
                                                        class="report-desc">$description</span><br/><% end_if %>
                                            </dd>
                                        <% end_loop %>
                                    </dl>
                                    <% end_if %>
                                <% end_loop %>
                            </dl>
                        <% end_if %>
                    </div>
                </div>
            </div>

        <% end_if %>

    </div>
</div>
