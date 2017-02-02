<?php

class Search_Categories_Metabox {

    /**
     * @const PLUGIN_SLUG
     */
    CONST PLUGIN_SLUG = 'search-categories-metabox';

    /**
     * @const TEXT_DOMAIN
     */
    CONST TEXT_DOMAIN = 'search-categories-metabox';

    /**
     * @var string $plugin_file
     */
    protected $plugin_file;

    /**
     * @var string $plugin_version
     */
    public $plugin_version = '0.1.0';

    /**
     * Search_Categories_Metabox constructor.
     * @param $file
     */
    public function __construct( $file ) {
        $this->plugin_file = $file;
    }

    /**
     * Gets the Post taxonomy Category slug by Post ID.
     *
     * @since 0.1.0
     * @access private
     *
     * @param $post_id
     * @return array
     */
    private function get_post_taxonomy( $post_id ) {
        $taxonomy = get_post_taxonomies( $post_id );
        $taxonomy = $taxonomy[0];
        return $taxonomy;
    }

    /**
     * Saves the Post Categories to DB.
     *
     * @since 0.1.0
     */
    public function save_post_categories() {
        global $post;

        $post_taxonomy       = $this->get_post_taxonomy( $post->ID );
        $selected_categories = $_POST['post-category'];
        if ( isset( $selected_categories ) && ! empty( $selected_categories ) ) {
            $selected_categories = array_map( 'intval', $selected_categories );
            $selected_categories = array_unique( $selected_categories );
            wp_set_object_terms( $post->ID, $selected_categories, $post_taxonomy, false );
        }
    }

    /**
     * Gets the array of categories.
     *
     * @since 0.1.0
     * @access private
     *
     * @param string $search_term
     * @return array
     */
    protected function _get_taxonomy_categories( $search_term = '' ) {
        $categories = array();
        $args       = array();
        $taxonomy   = array();

        /*
         * Get only Category taxonomy
         * Refer https://codex.wordpress.org/Function_Reference/get_post_taxonomies
         */
        $taxonomy   = $this->get_post_taxonomy();

        $args = array(
            'hide_empty' => false,
            'order_by'   => 'term_id',
            'order'      => 'ASC',
            'taxonomy'   => $taxonomy,
        );

        if ( trim( $search_term ) != '' ) {

            $additional_args = array(
                'name__like' => $search_term,
                'number'     => 10,
            );

            $args = wp_parse_args( $additional_args, $args );
        }

        $categories = get_categories( $args );
        return $categories;
    }

    /**
     * Renders the Category div in the posts editor.
     *
     * @since 0.1.0
     */
    public function search_category_div_callback() {
    ?>

        <p>
            <input id="scm-search-categories-search" type="text" name="scm-search-categories-search" size="16" />
            <input type="button" class="button" id="scm-search-clear" value="Clear" />
        </p>

        <div class="scm-categories-panel">
            <ul id="scm-categories-ul">
            <?php
            global $post;
            $categories = $this->_get_taxonomy_categories();
            $post_taxonomy = $this->get_post_taxonomy( $post->ID );
            $post_categories = get_the_terms( $post->ID, $post_taxonomy );
            $post_categories = array_column( $post_categories, 'term_id' );
            foreach( $categories as $category ) :
                $is_post_in_category = in_array( $category->term_id, $post_categories, true );
            ?>
                <li id='<?php echo $category->term_id; ?>'>
                    <label class="selecttit">
                        <input id='in-category-<?php echo $category->term_id; ?>' class="scm-category-list-item" value=<?php echo $category->term_id; ?> name="post-category[]" type="checkbox" <?php if ( $is_post_in_category ) { echo 'checked="checked"'; } ?>> <?php echo $category->name; ?>
                    </label>
                </li>
            <?php
            endforeach;
            ?>
            </ul>

        </div>


        <!--
        <div id="category-adder" class="">
            <a id="scm-category-add-toggle" href="#category-add" class="taxonomy-add-new">
                + Add New Category</a>
            <p id="scm-category-add" class="category-add wp-hidden-child">
                <label class="screen-reader-text" for="newcategory">Add New Category</label>
                <input name="newcategory" id="newcategory" class="form-required" value="New Category Name" aria-required="true" type="text">
                <label class="screen-reader-text" for="newcategory_parent">
                    Parent Category: </label>
                <select name="newcategory_parent" id="newcategory_parent" class="postform">
                    <option class="level-0" value="1">Uncategorized</option>
                </select>
                <input id="category-add-submit" data-wp-lists="add:categorychecklist:category-add" class="button category-add-submit" value="Add New Category" type="button">
                <input id="_ajax_nonce-add-category" name="_ajax_nonce-add-category" value="d68b600aac" type="hidden"><span id="category-ajax-response"></span>
            </p>
        </div>
        -->

    <?php
    }

    /**
     * Adds Categories metabox with the Search feature.
     *
     * @since 0.1.0
     */
    public function add_search_categories_metabox() {

        add_meta_box( 'searchcategorydiv', 'Categories', array( $this, 'search_category_div_callback' ), 'post', 'side', 'default', null );

    }

    /**
     * Callback for the AJAX request.
     *
     * @since 0.1.0
     */
    public function search_categories_callback() {

        check_ajax_referer( 'search-categories-metabox', 'security' );

        $search_query        = $_POST['query'];
        $selected_categories = $_POST['categories'];
        $result              = [];
        $post_id             = $_POST['post_id'];

        if ( empty( $selected_categories ) ) {
            $selected_categories = array();
        }

        if( isset( $search_query ) && ! is_null( $search_query ) ) {
            $categories = $this->_get_taxonomy_categories( $search_query );
        } else {
            $categories = $this->_get_taxonomy_categories();
        }

        $result['category_list'] = '';
        $result['selected_categories'] = $selected_categories;

        if( ! empty( $categories ) ) {
            ob_start();
            foreach ($categories as $category) {
                $is_user_selected = in_array( $category->term_id, $selected_categories );
                ?>

                <li id='<?php echo $category->term_id; ?>'>
                    <label class="selecttit"><input id='in-category-<?php echo $category->term_id; ?>' class="scm-category-list-item" value=<?php echo $category->term_id; ?> name="post-category[]" type="checkbox" <?php if( $is_user_selected ) { echo 'checked="checked"'; } ?>> <?php echo $category->name; ?>
                </li>

                <?php
            }
            $result['category_list'] = ob_get_clean();
        }

        //echo json_encode( 'Daniel' );
        wp_send_json_success( $result );
        wp_die();
    }

    /**
     * Loads the Javascripts requried for the Admin section.
     *
     * @since 0.1.0
     */
    public function load_admin_scripts() {

        global $post;

        wp_enqueue_script( 'admin_post_editor_script', plugin_dir_url( $this->plugin_file ) . 'assets/js/search-categories-metabox.js', array( 'jquery' ), $this->plugin_version );

        wp_localize_script( 'admin_post_editor_script', 'ajax_object', array(
            'ajax_url'                      => admin_url( 'admin-ajax.php' ),
            'ajax_nonce'                    => wp_create_nonce( "search-categories-metabox" ),
            'post_id'                       => $post->ID,
            'no_results_error_message'      => __( 'No results found.', self::TEXT_DOMAIN ),
            'something_wrong_error_message' => __( 'Something went wrong.', self::TEXT_DOMAIN )
        ) );

    }

    /**
     * Loads the CSS required for the Admin section.
     *
     * @since 0.1.0
     */
    public function load_admin_styles() {

        wp_enqueue_style( 'scm_admin_post_editor_css', plugin_dir_url( $this->plugin_file ) . 'assets/css/admin-style.css', false, $this->plugin_version );

    }

    /**
     * Renders the plugin's settings page in the Dashboard.
     *
     * @since 0.1.0
     */
    public function render_search_categories_metabox_page() {

        ?>
        <div class="wrap">

            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

            <form action='<?php echo esc_html( $_SERVER['REQUEST_URI'] ); ?>' method='POST'>
                <?php
                submit_button( 'Save Settings' );
                ?>
            </form>

        </div>
        <?php

    }

    /**
     * Adds the options page in the Admin Dashboard.
     *
     * @since 0.1.0
     */
    public function add_admin_menu() {

        add_options_page(
            __( 'Search Categories Metabox', Search_Categories_Metabox::TEXT_DOMAIN ),
            __( 'Search Categories Mb', Search_Categories_Metabox::TEXT_DOMAIN ),
            'manage_options',
            Search_Categories_Metabox::PLUGIN_SLUG,
            array( $this, 'render_search_categories_metabox_page' )
        );

    }

    /**
     * Removes the default `Category` metabox from the WordPress editor.
     *
     * @since 0.1.0
     */
    public function remove_default_categories_metabox() {

        //remove_meta_box( 'categorydiv', 'post' );

    }

    /**
     * Defines the admin hooks.
     *
     * @since 0.1.0
     */
    public function define_admin_hooks() {
        add_action( 'admin_init', array( $this, 'remove_default_categories_metabox' ) );
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
        add_action( 'wp_ajax_search_categories', array( $this, 'search_categories_callback' ) );
        add_action( 'admin_init', array( $this, 'add_search_categories_metabox' ) );
        add_action( 'save_post', array( $this, 'save_post_categories' ) );
    }

    /**
     * Invokes the hooks.
     *
     * @since 0.1.0
     */
    public function run() {
        $this->define_admin_hooks();
    }

}