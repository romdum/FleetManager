<div class="wrap">
	<header>
        <h1>Options</h1>
	</header>
    <form method="post" action="<?= admin_url('admin-post.php?action=FM_save_settings') ?>">
    	<?php wp_nonce_field( 'FM_save_settings' ) ?>
    	<div class="settingGroup">
    		<header>
    			<h2>Général</h2>
    		</header>
        	<?php $this->displayOption( $settings['main'] ) ?>
    	</div>
        <div class="settingGroup">
    		<header>
    			<h2>Réseaux Sociaux</h2>
    		</header>
        	<?php $this->displayOption( $settings['socialNetwork'] ) ?>
    	</div>
        <input type="submit" value="Sauvegarder" class="btn">
    </form>
    <form method="post" action="<?= admin_url('admin-post.php?action=FM_transfer') ?>" id="transferForm" enctype="multipart/form-data">
    	<?php wp_nonce_field( 'FM_transfer' ) ?>
    	<header>
    		<h2>Importer / Exporter</h2>
    	</header>
        <div>
            <label for="dataToTransfer" class="settingLabel label">Données à transférer</label>
            <select name="dataToTransfer" class="select" id="dataToTransfer">
                <option value="<?= \FleetManager\Vehicle\PostType::POST_TYPE_NAME ?>">Véhicules</option>
                <option value="<?= \FleetManager\Transfer\Brand::NAME ?>">Marques / Modèles</option>
            </select>
        </div>
        <div>
            <label for="formatToExport" class="settingLabel label">Format</label>
            <select name="formatToExport" id="formatToExport" class="select">
                <option value="<?= \FleetManager\Transfer\DataParser::CSV ?>">CSV</option>
            </select>
        </div>
    	<input type="hidden" name="postTypeName" value="<?= \FleetManager\Vehicle\PostType::POST_TYPE_NAME ?>" class="btn">
    	<input type="submit" name="<?= \FleetManager\Transfer\Transfer::IMPORT ?>" value="Importer" class="btn" id="btnImport">
    	<input type="submit" name="<?= \FleetManager\Transfer\Transfer::EXPORT ?>" value="Exporter" class="btn">
        <input type="file" id="selectedFile" style="visibility:hidden;" name="importFile">
    </form>
</div>