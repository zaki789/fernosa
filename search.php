<?php
/**
 * Search results template (Fernosa)
 * - Focused on WooCommerce products
 */
defined( 'ABSPATH' ) || exit;

get_header();

$term = get_search_query();

// Force product search query on this template (in case header form didn't set post_type)
$paged = max( 1, get_query_var( 'paged' ) );

$args = array(
	'post_type'      => 'product',
	'post_status'    => 'publish',
	's'              => $term,
	'posts_per_page' => 12,
	'paged'          => $paged,
);

$q = new WP_Query( $args );
?>
<main id="primary" class="site-main fg-search-page">
	<section class="fg-search-hero">
		<div class="fg-search-hero__inner">
			<h1 class="fg-search-title">
				<?php echo esc_html__( 'نتایج جستجو برای:', 'fernosa-gelato' ); ?>
				<span class="fg-search-term"><?php echo esc_html( $term ); ?></span>
			</h1>
			<p class="fg-search-sub"><?php echo esc_html__( 'محصولات پیدا شده را همین‌جا ببینید و جزئیات هر محصول را مشاهده کنید.', 'fernosa-gelato' ); ?></p>
		</div>
	</section>

	<section class="fg-search-results">
		<?php if ( $q->have_posts() ) : ?>
			<div class="fg-product-grid">
				<?php while ( $q->have_posts() ) : $q->the_post(); global $product; ?>
					<?php if ( ! $product || ! is_a( $product, 'WC_Product' ) ) { $product = wc_get_product( get_the_ID() ); } ?>
					<article class="fg-product-card" id="fg-product-<?php the_ID(); ?>">
						<a class="fg-product-thumb" href="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php the_title_attribute(); ?>">
							<?php
							if ( has_post_thumbnail() ) {
								echo get_the_post_thumbnail( get_the_ID(), 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) );
							} else {
								echo wc_placeholder_img( 'woocommerce_thumbnail' );
							}
							?>
						</a>

						<div class="fg-product-body">
							<h2 class="fg-product-title">
								<a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a>
							</h2>

							<div class="fg-product-excerpt">
								<?php
								$short = '';
								if ( $product && method_exists( $product, 'get_short_description' ) ) {
									$short = wp_strip_all_tags( $product->get_short_description() );
								}
								if ( ! $short ) {
									$short = wp_strip_all_tags( get_the_excerpt() );
								}
								echo esc_html( wp_trim_words( $short, 14, '…' ) );
								?>
							</div>

							<div class="fg-product-meta">
								<div class="fg-product-price">
									<?php echo wp_kses_post( $product ? $product->get_price_html() : '' ); ?>
								</div>
                <div class="fg-product-actions">
                  <a class="btn btn-ghost" href="<?php echo esc_url( get_permalink() ); ?>">مشاهده محصول</a>
                </div>
              </div>
            </div>
					</article>
				<?php endwhile; ?>
			</div>

			<div class="fg-pagination">
				<?php
				echo paginate_links( array(
					'total'   => $q->max_num_pages,
					'current' => $paged,
					'prev_text' => 'قبلی',
					'next_text' => 'بعدی',
				) );
				?>
			</div>
		<?php else : ?>
			<div class="fg-empty">
				<div class="fg-empty__card">
					<h2><?php echo esc_html__( 'چیزی پیدا نشد', 'fernosa-gelato' ); ?></h2>
					<p><?php echo esc_html__( 'اسم محصول را کمی متفاوت بنویسید یا یک کلمه کوتاه‌تر امتحان کنید.', 'fernosa-gelato' ); ?></p>
				</div>
			</div>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?>
	</section>
</main>
<?php
get_footer();
