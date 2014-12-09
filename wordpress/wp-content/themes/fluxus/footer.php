<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package fluxus
 * @since fluxus 1.0
 */
?>

    <div id="footer-push"></div>
</div><!-- #page-wrapper -->

<footer id="footer">
    <div class="footer-inner clearfix">
        <?php do_action( 'footer_social' ); ?>
        <div class="footer-links"><?php

            // Show menu, if it has been assigned.
            if ( has_nav_menu( 'footer_primary' ) ): ?>
                <nav class="footer-navigation"><?php
                    @wp_nav_menu( array( 'theme_location' => 'footer_primary', 'walker' => new Intheme_Menu_Walker() ) ); ?>
                </nav><?php
            endif;

            $copyright = of_get_option( 'fluxus_copyright_text' );
            if ( !empty( $copyright ) ) : ?>
                <div class="credits"><?php echo $copyright; ?></div><?php
            endif;

            ?>
        </div>
        <div class="nav-tip">
            <?php printf( __( 'Use arrows %s for navigation', 'fluxus' ), '<a href="#" class="button-minimal icon-left-open-mini" id="key-left"></a><a href="#" class="button-minimal icon-right-open-mini" id="key-right"></a>' ); ?>
        </div>
        <?php do_action( 'footer_links' ); ?>
    </div>
</footer>
<?php

wp_footer();

?>
</body>
</html>