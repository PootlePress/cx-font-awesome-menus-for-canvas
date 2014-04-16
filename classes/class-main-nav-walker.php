<?php
/**
 * Create HTML list of nav menu items.
 * @uses Walker
 */
class Pootlepress_FA_Main_Nav_Walker extends Walker {
    /**
     * What the class handles.
     *
     * @see Walker::$tree_type
     * @since 3.0.0
     * @var string
     */
    var $tree_type = array( 'post_type', 'taxonomy', 'custom' );

    /**
     * Database fields to use.
     *
     * @see Walker::$db_fields
     * @since 3.0.0
     * @todo Decouple this.
     * @var array
     */
    var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

    /**
     * Starts the list before the elements are added.
     *
     * @see Walker::start_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     */
    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }

    /**
     * Ends the list of after the elements are added.
     *
     * @see Walker::end_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     */
    function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    /**
     * Start the element output.
     *
     * @see Walker::start_el()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item   Menu item data object.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     * @param int    $id     Current item ID.
     */
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $class_names = $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        $temp = array();
        $iconClass = '';
        foreach ($classes as $class) {
            if (strpos($class, 'icon-') !== 0) {
                $temp[] = $class;
            } else {
                $iconClass= $class;
            }
        }

        $classes = $temp;

        /**
         * Filter the CSS class(es) applied to a menu item's <li>.
         *
         * @since 3.0.0
         *
         * @param array  $classes The CSS classes that are applied to the menu item's <li>.
         * @param object $item    The current menu item.
         * @param array  $args    An array of arguments. @see wp_nav_menu()
         */
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        /**
         * Filter the ID applied to a menu item's <li>.
         *
         * @since 3.0.1
         *
         * @param string The ID that is applied to the menu item's <li>.
         * @param object $item The current menu item.
         * @param array $args An array of arguments. @see wp_nav_menu()
         */
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= $indent . '<li' . $id . $value . $class_names .'>';

        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

        /**
         * Filter the HTML attributes applied to a menu item's <a>.
         *
         * @since 3.6.0
         *
         * @param array $atts {
         *     The HTML attributes applied to the menu item's <a>, empty strings are ignored.
         *
         *     @type string $title  The title attribute.
         *     @type string $target The target attribute.
         *     @type string $rel    The rel attribute.
         *     @type string $href   The href attribute.
         * }
         * @param object $item The current menu item.
         * @param array  $args An array of arguments. @see wp_nav_menu()
         */
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';


        $iconPos = '';
        $iconSize = '';
        $labelRemove = '';
        if ($depth == 0) {
            $iconPos = get_option('pootlepress-fa-menu-primary-nav-icon-pos', 'Left of nav label');
            $iconSize = get_option('pootlepress-fa-menu-primary-nav-icon-size', 'Large');
            $labelRemove = get_option('pootlepress-fa-menu-primary-nav-label-remove', 'false');
        } else {
            $iconPos = get_option('pootlepress-fa-menu-primary-nav-sub-menu-icon-pos', 'Left of nav label');
            $iconSize = get_option('pootlepress-fa-menu-primary-nav-sub-menu-icon-size', 'Large');
        }

        $sizeClass = 'icon-large';
        switch ($iconSize) {
            case 'Large':
                $sizeClass = 'icon-large';
                break;
            case '2x':
                $sizeClass = 'icon-2x';
                break;
            case '3x':
                $sizeClass = 'icon-3x';
                break;
            case '4x':
                $sizeClass = 'icon-4x';
                break;
            default:
                $sizeClass = 'icon-large';
                break;
        }        switch ($iconSize) {
            case 'Large':
                $sizeClass = 'icon-large';
                break;
            case '2x':
                $sizeClass = 'icon-2x';
                break;
            case '3x':
                $sizeClass = 'icon-3x';
                break;
            case '4x':
                $sizeClass = 'icon-4x';
                break;
            default:
                $sizeClass = 'icon-large';
                break;
        }

        // Add icon
        if ($iconClass != '' && $iconPos !== 'Below nav label(centered)') {
            $item_output .= "<i class='" . esc_attr($iconClass . ' ' . $sizeClass) . "'></i>";
        }

        /** This filter is documented in wp-includes/post-template.php */
        if ($labelRemove == 'true' && $depth == 0) {
            $item_output .= $args->link_before . $args->link_after;
        } else {
            $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
        }

        // Add icon
        if ($iconClass != '' && $iconPos === 'Below nav label(centered)') {
            $item_output .= "<i class='" . esc_attr($iconClass . ' ' . $sizeClass) . "'></i>";
        }

        $item_output .= '</a>';
        $item_output .= $args->after;

        /**
         * Filter a menu item's starting output.
         *
         * The menu item's starting output only includes $args->before, the opening <a>,
         * the menu item's title, the closing </a>, and $args->after. Currently, there is
         * no filter for modifying the opening and closing <li> for a menu item.
         *
         * @since 3.0.0
         *
         * @param string $item_output The menu item's starting HTML output.
         * @param object $item        Menu item data object.
         * @param int    $depth       Depth of menu item. Used for padding.
         * @param array  $args        An array of arguments. @see wp_nav_menu()
         */
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    /**
     * Ends the element output, if needed.
     *
     * @see Walker::end_el()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item   Page data object. Not used.
     * @param int    $depth  Depth of page. Not Used.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     */
    function end_el( &$output, $item, $depth = 0, $args = array() ) {
        $output .= "</li>\n";
    }

} // Walker_Nav_Menu