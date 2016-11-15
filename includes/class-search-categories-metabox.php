<?php

class Search_Categories_Metabox {

    CONST PLUGIN_SLUG = 'search-categories-metabox';

    CONST TEXT_DOMAIN = 'search-categories-metabox';

    public function render_search_categories_metabox_page() {

    ?>
        <div class="wrap">

            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

            <form action='<?php echo esc_html( $_SERVER['REQUEST_URI'] ); ?>' method='POST'>
                <?php
                submit_button('Save Settings');
                ?>
            </form>

        </div>
    <?php

    }

    public function search_categories_metabox_menu() {

        add_options_page(
            __( 'Search Categories Metabox', Search_Categories_Metabox::TEXT_DOMAIN ),
            __( 'Search Categories Mb', Search_Categories_Metabox::TEXT_DOMAIN ),
            'manage_options',
            Search_Categories_Metabox::PLUGIN_SLUG,
            array( $this, 'render_search_categories_metabox_page' )
        );

    }

    private function define_public_hooks() {

        add_action( 'admin_menu', array( $this, 'search_categories_metabox_menu' ) );

    }

    public function run() {

        $this->define_public_hooks();


    }

}