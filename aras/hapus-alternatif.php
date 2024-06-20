<?php require_once('includes/init.php'); ?>

<?php
$ada_error = false;
$result = '';

$id_alternatif = (isset($_GET['id'])) ? trim($_GET['id']) : '';

if (!$id_alternatif) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = $pdo->prepare('SELECT id_alternatif FROM tb_alternatif WHERE id_alternatif = :id_alternatif');
	$query->execute(array('id_alternatif' => $id_alternatif));
	$result = $query->fetch();

	if (empty($result)) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	} else {

		$handle = $pdo->prepare('DELETE FROM tb_nilai WHERE id_alternatif = :id_alternatif');
		$handle->execute(array(
			'id_alternatif' => $result['id_alternatif']
		));
		$handle = $pdo->prepare('DELETE FROM tb_alternatif WHERE id_alternatif = :id_alternatif');
		$handle->execute(array(
			'id_alternatif' => $result['id_alternatif']
		));
		redirect_to('list-alternatif.php?status=sukses-hapus');
	}
}
?>

<?php
$judul_page = 'Hapus Alternatif';
require_once('template-parts/header.php');
?>

<div class="main-content-row">
	<div class="container clearfix">

		<?php include_once('template-parts/sidebar-alternatif.php'); ?>

		<div class="main-content the-content">
			<h1><?php echo $judul_page; ?></h1>

			<?php if ($ada_error) : ?>

				<?php echo '<p>' . $ada_error . '</p>'; ?>

			<?php endif; ?>

		</div>

	</div><!-- .container -->
</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');
