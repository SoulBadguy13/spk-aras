<?php require_once('includes/init.php'); ?>

<?php
$ada_error = false;
$result = '';

$id_alternatif = (isset($_GET['id'])) ? trim($_GET['id']) : '';

if (!$id_alternatif) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = $pdo->prepare('SELECT * FROM tb_alternatif WHERE id_alternatif = :id_alternatif');
	$query->execute(array('id_alternatif' => $id_alternatif));
	$result = $query->fetch();

	if (empty($result)) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	}
}
?>

<?php
$judul_page = 'Detail Alternatif';
require_once('template-parts/header.php');
?>

<div class="main-content-row">
	<div class="container clearfix">

		<?php include_once('template-parts/sidebar-alternatif.php'); ?>

		<div class="main-content the-content">
			<h1><?php echo $judul_page; ?></h1>

			<?php if ($ada_error) : ?>

				<?php echo '<p>' . $ada_error . '</p>'; ?>

			<?php elseif (!empty($result)) : ?>

				<h4>Alternatif</h4>
				<p><?php echo $result['no_alternatif']; ?></p>

				<h4>Nama Alternatif</h4>
				<p><?php echo nl2br($result['nama_alternatif']); ?></p>

				<h4>Tanggal Input</h4>
				<p><?php
					$tgl = strtotime($result['tanggal_input']);
					echo date('j F Y', $tgl);
					?></p>

				<?php
				$query2 = $pdo->prepare('SELECT tb_nilai.nilai AS nilai, tb_kriteria.nama_kriteria AS nama_kriteria FROM tb_kriteria 
				LEFT JOIN tb_nilai ON tb_nilai.id_kriteria = tb_kriteria.id_kriteria 
				AND tb_nilai.id_alternatif = :id_alternatif ORDER BY tb_kriteria.id_kriteria ASC');
				$query2->execute(array(
					'id_alternatif' => $id_alternatif
				));
				$query2->setFetchMode(PDO::FETCH_ASSOC);
				$kriterias = $query2->fetchAll();
				if (!empty($kriterias)) :
				?>
					<h3>Nilai Kriteria</h3>
					<table class="pure-table">
						<thead>
							<tr>
								<?php foreach ($kriterias as $kriteria) : ?>
									<th><?php echo $kriteria['nama_kriteria']; ?></th>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody>
							<tr>
								<?php foreach ($kriterias as $kriteria) : ?>
									<th><?php echo ($kriteria['nilai']) ? $kriteria['nilai'] : 0; ?></th>
								<?php endforeach; ?>
							</tr>
						</tbody>
					</table>
				<?php
				endif;
				?>

				<p><a href="edit-alternatif.php?id=<?php echo $id_alternatif; ?>" class="button"><span class="fa fa-pencil"></span> Edit</a> &nbsp; <a href="hapus-alternatif.php?id=<?php echo $id_alternatif; ?>" class="button button-red yaqin-hapus"><span class="fa fa-times"></span> Hapus</a></p>

			<?php endif; ?>

		</div>

	</div><!-- .container -->
</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');
