
</main>

<?php
// Hide footer on single project details pages
if ( ! is_singular( 'projects' ) ) :
?>
<footer class="footer">
  <div class="container">
    <div class="footer__wrapper">
      
      <!-- Logo and Contact Column -->
      <div class="footer__col-logo">
        <a class="footer__logo" href="/" aria-label="JLB Partners - Return to homepage">
			<img 
			src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/icons/footer-logo.svg"
			alt=""
			width="89"
			height="87"
			role="presentation"
			/>
		</a>

        
        <address class="footer__address">
          <?php
          $footer_address = get_option( 'golden_template_footer_address', '' );
          if ( ! empty( $footer_address ) ) {
            echo wp_kses_post( $footer_address );
          }
          ?>
        </address>
      </div>
		<div class="footer__nav-wrapper">
      <!-- Navigation Columns -->
      <nav class="footer__nav" aria-label="Footer Navigation">
        <?php golden_template_footer_menu( 'footer' ); ?>
      </nav>
       <div class="footer__bottom">
      <a class="footer__privacy" href="<?php echo esc_url( home_url( '/privacy-policy' ) ); ?>" title="Privacy Policy">Privacy Policy<img 
		src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/icons/arrow.svg" 
		alt="" 
		class="footer__icon"
		/>
		</a>
      
      <p class="footer__copyright_desktop"><?php echo do_shortcode( '[jlb_copyright format="desktop"]' ); ?></p>
      <p class="footer__copyright_mobile"><?php echo do_shortcode( '[jlb_copyright format="mobile"]' ); ?></p>
    </div>
	</div>

    </div>

    <!-- Bottom Section -->
   

  </div>
</footer>
<?php endif; ?>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>