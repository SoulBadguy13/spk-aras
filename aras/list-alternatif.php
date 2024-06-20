<?php require_once('includes/init.php'); ?>

<?php
$judul_page = 'List Alternatif';
require_once('template-parts/header.php');
?>

<div class="main-content-row">
	<div class="container clearfix">

		<?php include_once('template-parts/sidebar-alternatif.php'); ?>

		<div class="main-content the-content">

			<?php
			$status = isset($_GET['status']) ? $_GET['status'] : '';
			$msg = '';
			switch ($status):
				case 'sukses-baru':
					$msg = 'Data alternatif baru berhasil ditambahkan';
					break;
				case 'sukses-hapus':
					$msg = 'Alternatif berhasil dihapus';
					break;
				case 'sukses-edit':
					$msg = 'Alternatif behasil diedit';
					break;
			endswitch;

			if ($msg) :
				echo '<div class="msg-box msg-box-full">';
				echo '<p><span class="fa fa-bullhorn"></span> &nbsp; ' . $msg . '</p>';
				echo '</div>';
			endif;
			?>

			<h1>List Alternatif</h1>

			<?php
			$query = $pdo->prepare('SELECT * FROM tb_alternatif');
			$query->execute();
			// menampilkan berupa nama field
			$query->setFetchMode(PDO::FETCH_ASSOC);

			if ($query->rowCount() > 0) :
			?>

				<table class="pure-table pure-table-striped">
					<thead>
						<tr>
							<th>Alternatif</th>
							<th>Nama</th>
							<th>Detail</th>
							<th>Edit</th>
							<th>Hapus</th>
						</tr>
					</thead>
					<tbody>
						<?php while ($hasil = $query->fetch()) : ?>
							<tr>
								<td><?php echo $hasil['no_alternatif']; ?></td>
								<td><?php echo $hasil['nama_alternatif']; ?></td>
								<td><a href="single-alternatif.php?id=<?php echo $hasil['id_alternatif']; ?>"><span class="fa fa-eye"></span> Detail</a></td>
								<td><a href="edit-alternatif.php?id=<?php echo $hasil['id_alternatif']; ?>"><span class="fa fa-pencil"></span> Edit</a></td>
								<td><a href="hapus-alternatif.php?id=<?php echo $hasil['id_alternatif']; ?>" class="red yaqin-hapus"><span class="fa fa-times"></span> Hapus</a></td>
							</tr>
						<?php endwhile; ?>
					</tbody>
				</table>


				<!-- STEP 1. Matriks Keputusan(X) ==================== -->
				<?php
				// Fetch semua kriteria
				$query = $pdo->prepare('SELECT id_kriteria, nama_kriteria, jenis_kriteria, bobot FROM tb_kriteria
				ORDER BY id_kriteria ASC');
				$query->execute();
				$kriterias = $query->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);

				// Fetch semua kambing
				$query2 = $pdo->prepare('SELECT id_alternatif, no_alternatif FROM tb_alternatif');
				$query2->execute();
				$query2->setFetchMode(PDO::FETCH_ASSOC);
				$kambings = $query2->fetchAll();
				?>

				<h3>Matriks Keputusan (X)</h3>
				<table class="pure-table pure-table-striped">
					<thead>
						<tr class="super-top">
							<th rowspan="2" class="super-top-left">Alternatif</th>
							<th colspan="<?php echo count($kriterias); ?>">Kriteria</th>
						</tr>
						<tr>
							<?php foreach ($kriterias as $kriteria) : ?>
								<th><?php echo $kriteria['nama_kriteria']; ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($kambings as $kambing) : ?>
							<tr>
								<td><?php echo $kambing['no_alternatif']; ?></td>
								<?php
								// Ambil Nilai
								$query3 = $pdo->prepare('SELECT id_kriteria, nilai FROM tb_nilai
								WHERE id_alternatif = :id_alternatif');
								$query3->execute(array(
									'id_alternatif' => $kambing['id_alternatif']
								));
								$query3->setFetchMode(PDO::FETCH_ASSOC);
								$nilais = $query3->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);

								foreach ($kriterias as $id_kriteria => $values) :
									echo '<td>';
									if (isset($nilais[$id_kriteria])) {
										echo $nilais[$id_kriteria]['nilai'];
										$kriterias[$id_kriteria]['nilai'][$kambing['id_alternatif']] = $nilais[$id_kriteria]['nilai'];
									} else {
										echo 0;
										$kriterias[$id_kriteria]['nilai'][$kambing['id_alternatif']] = 0;
									}

									if (isset($kriterias[$id_kriteria]['tn_kuadrat'])) {
										$kriterias[$id_kriteria]['tn_kuadrat'] += pow($kriterias[$id_kriteria]['nilai'][$kambing['id_alternatif']], 2);
									} else {
										$kriterias[$id_kriteria]['tn_kuadrat'] = pow($kriterias[$id_kriteria]['nilai'][$kambing['id_alternatif']], 2);
									}
									echo '</td>';
								endforeach;
								?>
								</pre>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

			<?php else : ?>
				<p>Maaf, belum ada data alternatif.</p>
			<?php endif; ?>
		</div>

	</div><!-- .container -->
</div><!-- .main-content-row -->

<?php
require_once('template-parts/footer.php');
