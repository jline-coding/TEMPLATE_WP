<?php $breadcrumbs = mytheme_breadcrumb(); ?>
<ul class="c-breadcrumb">

    <?php foreach ($breadcrumbs as $item) : ?>

        <li class="c-breadcrumb__item">

            <?php if (!empty($item['url'])) : ?>

                <a href="<?php echo esc_url($item['url']); ?>">
                    <?php echo esc_html($item['title']); ?>
                </a>

            <?php else : ?>

                <?php echo esc_html($item['title']); ?>

            <?php endif; ?>

        </li>

    <?php endforeach; ?>

</ul>