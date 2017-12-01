<p>
    <a href="<?= $args['link'] ?>"><?= $args['title'] ?></a>
</p>
<p class="subtitle lastVehiculePublishWidget">
    Le <?= $args['creationDate'] ?>
    <?php if( isset( $args['author'] ) ): ?>
    par <?= $args['author'] ?>
    <?php endif; ?>
</p>