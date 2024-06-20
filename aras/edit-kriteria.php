<?php require_once('includes/init.php'); ?>

<?php
$errors = array();
$sukses = false;

$ada_error = false;
$result = '';

$id_kriteria = (isset($_GET['id'])) ? trim($_GET['id']) : '';

if (!$id_kriteria) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = $pdo->prepare('SELECT * FROM tb_kriteria WHERE tb_kriteria.id_kriteria = :id_kriteria');
	$query->execute(array('id_kriteria' => $id_kriteria));
	$result = $query->fetch();

	if (empty($result)) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	}

	$id_kriteria = (isset($result['id_kriteria'])) ? trim($result['id_kriteria']) : '';
	$nama_kriteria = (isset($result['nama_kriteria'])) ? trim($result['nama_kriteria']) : '';
	$jenis_kriteria = (isset($result['jenis_kriteria'])) ? trim($result['jenis_kriteria']) : '';
	$bobot = (isset($result['bobot'])) ? trim($result['bobot']) : '';
	$jenis_nilai = (isset($result['cara_penilaian'])) ? $result['cara_penilaian'] : 0;
}

if (isset($_POST['submit'])) :

	$nama_kriteria = (isset($_POST['nama_kriteria'])) ? trim($_POST['nama_kriteria']) : '';
	$jenis_kriteria = (isset($_POST['jenis_kriteria'])) ? trim($_POST['jenis_kriteria']) : '';
	$bobot = (isset($_POST['bobot'])) ? trim($_POST['bobot']) : '';
	$pilihan = (isset($_POST['pilihan'])) ? $_POST['pilihan'] : '';
	$jenis_nilai = (isset($_POST['jenis_nilai'])) ? trim($_POST['jenis_nilai']) : 0;

	// Validasi Nama Kriteria
	if (!$nama_kriteria) {
		$errors[] = 'Nama kriteria tidak boleh kosong';
	}
	// Validasi Tipe
	if (!$jenis_kriteria) {
		$errors[] = 'Type kriteria tidak boleh kosong';
	}
	// Validasi Bobot
	if (!$bobot) {
		$errors[] = 'Bobot kriteria tidak boleh kosong';
	}

	// Jika lolos validasi lakukan hal di bawah ini
	if (empty($errors)) :

		$prepare_query = 'UPDATE tb_kriteria SET nama_kriteria = :nama_kriteria, jenis_kriteria = :jenis_kriteria, bobot = :bobot, cara_penilaian = :jenis_nilai WHERE id_kriteria = :id_kriteria';
		$data = array(
			'nama_kriteria' => $nama_kriteria,
			'jenis_kriteria' => $jenis_kriteria,
			'bobot' => $bobot,
			'id_kriteria' => $id_kriteria,
			'jenis_nilai' => $jenis_nilai
		);
		$handle = $pdo->prepare($prepare_query);
		$sukses = $handle->execute($data);


		// Simpan Nilai Kriteria
		$ids_pilihan = array();
		if (!empty($pilihan)) : foreach ($pilihan as $pil) :

				$keterangan = (isset($pil['keterangan'])) ? trim($pil['keterangan']) : '';
				$nilai = (isset($pil['nilai'])) ? floatval(trim($pil['nilai'])) : '';
				$id_parameter = (isset($pil['id'])) ? trim($pil['id']) : '';

				echo $nilai;
				if ($id_parameter && $keterangan != '' && ($nilai >= 0)) :
					// Update jika pilihan telah ada di database				
					$prepare_query = 'UPDATE tb_parameter SET keterangan = :keterangan, id_kriteria = :id_kriteria, nilai = :nilai WHERE id_parameter = :id_parameter';
					$data = array(
						'keterangan' => $keterangan,
						'id_kriteria' => $id_kriteria,
						'nilai' => $nilai,
						'id_parameter' => $id_parameter
					);
					$handle = $pdo->prepare($prepare_query);
					$sukses = $handle->execute($data);
					if ($sukses) :
						$ids_pilihan[] = $id_parameter;
					endif;

				elseif (($keterangan != '') && ($nilai >= 0)) :
					// Insert jika pilihan belum ada di database
					$prepare_query = 'INSERT INTO tb_parameter (keterangan, id_kriteria, nilai) VALUES (:keterangan, :id_kriteria, :nilai)';
					$data = array(
						'keterangan' => $keterangan,
						'id_kriteria' => $id_kriteria,
						'nilai' => $nilai
					);
					$handle = $pdo->prepare($prepare_query);
					$sukses = $handle->execute($data);
					if ($sukses) :
						$last_id = $pdo->lastInsertId();
						$ids_pilihan[] = $last_id;
					endif;

				endif;

			endforeach;
		endif; // end if(!empty($pilihan))

		// Bersihkan pilihan
		if (!empty($ids_pilihan)) :
			$not_in = implode(',', $ids_pilihan);
			$prepare_query = 'DELETE FROM tb_parameter WHERE id_kriteria = :id_kriteria AND id_parameter NOT IN (' . $not_in . ')';
			$handle = $pdo->prepare($prepare_query);
			$handle->execute(array('id_kriteria' => $id_kriteria));
		else :
			$prepare_query = 'DELETE FROM tb_parameter WHERE id_kriteria = :id_kriteria';
			$handle = $pdo->prepare($prepare_query);
			$handle->execute(array('id_kriteria' => $id_kriteria));
		endif;

		redirect_to('list-kriteria.php?status=sukses-edit');


	endif;

endif;
?>

<?php
$judul_page = 'Edit Kriteria';
require_once('template-parts/header.php');
?>

<div class="main-content-row">
	<div class="container clearfix">

		<?php include_once('template-parts/sidebar-kriteria.php'); ?>

		<div class="main-content the-content">
			<h1>Edit Kriteria</h1>

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

				<form action="edit-kriteria.php?id=<?php echo $id_kriteria; ?>" method="post">
					<div class="field-wrap clearfix">
						<label>Nama Kriteria <span class="red">*</span></label>
						<input type="text" name="nama_kriteria" value="<?php echo $nama_kriteria; ?>">
					</div>
					<div class="field-wrap clearfix">
						<label>Jenis Kriteria <span class="red">*</span></label>
						<select name="jenis_kriteria">
							<option value="benefit" <?php selected($jenis_kriteria, 'benefit'); ?>>Benefit</option>
							<option value="cost" <?php selected($jenis_kriteria, 'cost'); ?>>Cost</option>
						</select>
					</div>
					<div class="field-wrap clearfix">
						<label>Bobot Kriteria <span class="red">*</span></label>
						<input type="number" name="bobot" value="<?php echo $bobot; ?>" step="0.01">
					</div>
					<div class="field-wrap clearfix">
						<label>Cara Penilaian</label>
						<select name="jenis_nilai" id="jenis-nilai">
							<option value="0" <?php selected($jenis_nilai, 0); ?>>Inputan Langsung</option>
							<option value="1" <?php selected($jenis_nilai, 1); ?>>Menggunakan Pilihan Variabel</option>
						</select>
					</div>

					<?php
					$the_class = ($jenis_nilai == 0) ? 'sembunyikan' : '';
					?>
					<div class="field-wrap list-var clearfix <?php echo $the_class; ?>">
						<h3>Parameter</h3>
						<table id="pilihan-var" class="pure-table pure-table-striped">
							<thead>
								<tr>
									<th>Nama Parameter</th>
									<th style="width: 120px;">Nilai Parameter</th>
									<th>Hapus</th>
								</tr>
							</thead>
							<tbody>

								<?php
								$query = $pdo->prepare('SELECT * FROM tb_parameter WHERE id_kriteria = :id_kriteria ORDER BY id_kriteria ASC');
								$query->execute(array(
									'id_kriteria' => $id_kriteria
								));
								// menampilkan berupa nama field
								$query->setFetchMode(PDO::FETCH_ASSOC);
								$ctr = 1;
								if ($query->rowCount() > 0) : while ($results = $query->fetch()) :
								?>
										<tr data-counter="<?php echo $ctr; ?>">
											<td><input type="text" name="pilihan[<?php echo $ctr; ?>][keterangan]" value="<?php echo $results['keterangan']; ?>">
												<input type="hidden" name="pilihan[<?php echo $ctr; ?>][id]" value="<?php echo $results['id_parameter']; ?>">
											</td>
											<td><input type="text" name="pilihan[<?php echo $ctr; ?>][nilai]" value="<?php echo $results['nilai']; ?>"></td>
											<td><a href="#" class="red del-this-row"><span class="fa fa-times"></span>Hapus</a></td>
										</tr>
										<?php $ctr++; ?>
								<?php endwhile;
								endif; ?>

							</tbody>
						</table>
						<div class="align-right">
							<a href="#" class="button tambah-pilihan">Tambah Pilihan</a>
						</div>
					</div>

					<div class="field-wrap clearfix">
						<button type="submit" name="submit" value="submit" class="button">Simpan Kriteria</button>
					</div>
				</form>

			<?php endif; ?>

		</div>

	</div><!-- .container -->
</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');
