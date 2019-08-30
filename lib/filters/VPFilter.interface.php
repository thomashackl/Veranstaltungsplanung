<?php

/**
 * Interface VPFilter
 *
 * A VPFilter is an element in the sidebar that has lots of functionality in once. Most
 * important is to know that each VPFilter has a distinct context: either "courses",
 * "persons" or "resources". The static method VPFilter::context() returns exactly one
 * of these strings to indicate the context it is responsible for.
 *
 * Next it has a name returned by the method getName, which is only important to enable
 * or disable it.
 *
 * The method getSidebarWidget returns an element of type SidebarWidget. Remind that this
 * element has to have a CSS-class exactly like the name of the context. Usually you add
 * this CSS class through the method $mywidget->addLayoutCSSClass($context);
 *
 * Further more we need to know what the parameter name of the VPFilter is. Most of the
 * time your widget has only one input or select element. But once it comes to fancy
 * widgets like in VPResourceTreeFilter where you are able to select multiple resources
 * you might want to declare exactly what of the parameters is relevant for the filter.
 * You return that parameter-name with the method getParameterName().
 *
 * The method applyFilter(\Veranstaltungsplanung\SQLQuery $query) should add your filter
 * to the query. You can use Request::get($parameter_name) to get the parameter you are
 * looking for. It will always be available. But the $query itself is not just one $query
 * object but could be multiple different $queries. Sometimes it is a query object to
 * select the courses or sometimes it is a query to select course-dates. You should only
 * rely on the table that is specific for your context: auth_user_md5 for "persons",
 * seminare for "courses" and resources_objects for "resources".
 *
 */
interface VPFilter
{
    /**
     * Either "courses", "persons" or "resources". Indicates for which selected context this filter should be
     * available.
     * @return string
     */
    static public function context();

    /**
     * Name of the filter displayed in the configuration window.
     * @return string
     */
    public function getName();

    /**
     * Returns a widget (or null) that gets attached to the sidebar.
     * @return SidebarWidget
     */
    public function getSidebarWidget();

    /**
     * Name of the parameter that is sent by the sidebar widget. We need to know this to tunnel
     * it to the URL and fetch dates with it.
     * @return string
     */
    public function getParameterName();

    /**
     * Method executed to change the SQLQuery-object.
     * @param \Veranstaltungsplanung\SQLQuery $query
     * @return void
     */
    public function applyFilter(\Veranstaltungsplanung\SQLQuery $query);
}