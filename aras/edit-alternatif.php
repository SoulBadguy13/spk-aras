<?php require_once('includes/init.php'); ?>

<?php
$errors = array();
$sukses = false;

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

	$id_alternatif = (isset($result['id_alternatif'])) ? trim($result['id_alternatif']) : '';
	$no_alternatif = (isset($result['no_alternatif'])) ? trim($result['no_alternatif']) : '';
	$nama_alternatif = (isset($result['nama_alternatif'])) ? trim($result['nama_alternatif']) : '';
}

if (isset($_POST['submit'])) :

	$no_alternatif = (isset($_POST['no_alternatif'])) ? trim($_POST['no_alternatif']) : '';
	$nama_alternatif = (isset($_POST['nama_alternatif'])) ? trim($_POST['nama_alternatif']) : '';
	$kriteria = (isset($_POST['kriteria'])) ? $_POST['kriteria'] : array();

	// Validasi Alternatif
	if (!$id_alternatif) {
		$errors[] = 'ID Alternatif tidak ada';
	}
	// Validasi
	if (!$no_alternatif) {
		$errors[] = 'Alternatif tidak boleh kosong';
	}

	// Jika lolos validasi lakukan hal di bawah ini
	if (empty($errors)) :

		$prepare_query = 'UPDATE tb_alternatif SET no_alternatif = :no_alternatif, nama_alternatif = :nama_alternatif WHERE id_alternatif = :id_alternatif';
		$data = array(
			'no_alternatif' => $no_alternatif,
			'nama_alternatif' => $nama_alternatif,
			'id_alternatif' => $id_alternatif
		);
		$handle = $pdo->prepare($prepare_query);
		$sukses = $handle->execute($data);

		if (!empty($kriteria)) :
			foreach ($kriteria as $id_kriteria => $nilai) :
				$handle = $pdo->prepare('INSERT INTO tb_nilai (id_alternatif, id_kriteria, nilai) 
				VALUES (:id_alternatif, :id_kriteria, :nilai)
				ON DUPLICATE KEY UPDATE nilai = :nilai');
				$handle->execute(array(
					'id_alternatif' => $id_alternatif,
					'id_kriteria' => $id_kriteria,
					'nilai' => $nilai
				));
			endforeach;
		endif;

		redirect_to('list-alternatif.php?status=sukses-edit');

	endif;

endif;
?>

<?php
$judul_page = 'Edit Alternatif';
require_once('template-parts/header.php');
?>

<div class="main-content-row">
	<div class="container clearfix">

		<?php include_once('template-parts/sidebar-alternatif.php'); ?>

		<div class="main-content the-content">
			<h1>Edit Alternatif</h1>

			<?php if (!empty($errors)) : ?>

				<div class="msg-box warning-box">
					<p><strong>Error:</strong></p>
					<ul>
						<?php foreach ($errors as $error) : ?>
							<li><?php echo $error; ?></li>
						<?php endforeach; ?>
					</ul>
				</div>

			<?php endif; ?>

			<?php if ($sukses) : ?>

				<div class="msg-box">
					<p>Data berhasil disimpan</p>
				</div>

			<?php elseif ($ada_error) : ?>

				<p><?php echo $ada_error; ?></p>

			<?php else : ?>

				<form action="edit-alternatif.php?id=<?php echo $id_alternatif; ?>" method="post">
					<div class="field-wrap clearfix">
						<label>Alternatif <span class="red">*</span></label>
						<input type="text" name="no_alternatif" value="<?php echo $no_alternatif; ?>" readonly style="background-color: #bfbfbf;">
					</div>
					<div class="field-wrap clearfix">
						<label>Nama Alternatif</label>
						<textarea name="nama_alternatif" cols="30" rows="2"><?php echo $nama_alternatif; ?></textarea>
					</div>

					<h3>Nilai Kriteria</h3>
					<?php
					$query2 = $pdo->prepare('SELECT tb_nilai.nilai AS nilai, tb_kriteria.nama_kriteria AS nama_kriteria, tb_kriteria.id_kriteria AS id_kriteria, tb_kriteria.cara_penilaian AS jenis_nilai 
					FROM tb_kriteria LEFT JOIN tb_nilai 
					ON tb_nilai.id_kriteria = tb_kriteria.id_kriteria 
					AND tb_nilai.id_alternatif = :id_alternatif 
					ORDER BY tb_kriteria.id_kriteria ASC');
					$query2->execute(array(
						'id_alternatif' => $id_alternatif
					));
					$query2->setFetchMode(PDO::FETCH_ASSOC);

					if ($query2->rowCount() > 0) :

						while ($kriteria = $query2->fetch()) :
					?>
							<div class="field-wrap clearfix">
								<label><?php echo $kriteria['nama_kriteria']; ?></label>
								<?php if (!$kriteria['jenis_nilai']) : ?>
									<input type="number" step="0.001" name="kriteria[<?php echo $kriteria['id_kriteria']; ?>]" value="<?php echo ($kriteria['nilai']) ? $kriteria['nilai'] : 0; ?>">
								<?php else : ?>
									<select name="kriteria[<?php echo $kriteria['id_kriteria']; ?>]">
										<option value="0">-- Pilih Variabel --</option>
										<?php
										$query3 = $pdo->prepare('SELECT * FROM tb_parameter WHERE id_kriteria = :id_kriteria ORDER BY id_kriteria ASC');
										$query3->execute(array(
											'id_kriteria' => $kriteria['id_kriteria']
										));
										// menampilkan berupa nama field
										$query3->setFetchMode(PDO::FETCH_ASSOC);
										if ($query3->rowCount() > 0) : while ($hasl = $query3->fetch()) :
										?>
												<option value="<?php echo $hasl['nilai']; ?>" <?php selected($kriteria['nilai'], $hasl['nilai']); ?>><?php echo $hasl['keterangan']; ?></option>
										<?php
											endwhile;
										endif;
										?>
									</select>
								<?php endif; ?>
							</div>
					<?php
						endwhile;

					else :
						echo '<p>Kriteria masih kosong.</p>';
					endif;
					?>

					<div class="field-wrap clearfix">
						<button type="submit" name="submit" value="submit" class="button">Simpan Alternatif</button>
					</div>
				</form>

			<?php endif; ?>

		</div>

	</div><!-- .container -->
</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');
