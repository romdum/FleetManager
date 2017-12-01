<div class="<?= $args['class'] ?>">
    <label class="<?= $args['class'] ?>Label label" for="<?= $args['id'] ?>"><?= $args['label'] ?></label>
    <select class="<?= $args['class'] ?>Select select" name="<?= $args['id'] ?>">
        <option value="none">Non renseigné(e)</option>
        <?php foreach( $args['type'] as $key => $value ): ?>
            <option value="<?= $key ?>" <?= $args['value'] === $key ? 'selected' : '' ?>><?= $value ?></option>
        <?php endforeach; ?>
    </select>
</div>
