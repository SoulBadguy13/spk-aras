<?php
error_reporting(0);
/* ---------------------------------------------
 * SPK ARAS
 * ------------------------------------------- */

/* ---------------------------------------------
 * Konek ke database & load fungsi-fungsi
 * ------------------------------------------- */
require_once('includes/init.php');

/* ---------------------------------------------
 * Load Header
 * ------------------------------------------- */
$judul_page = 'Perankingan Menggunakan Metode ARAS';
require_once('template-parts/header.php');

/* ---------------------------------------------
 * Set jumlah digit di belakang koma
 * ------------------------------------------- */
$digit = 3;

/* ---------------------------------------------
 * Fetch semua kriteria
 * ------------------------------------------- */
$query = $pdo->prepare('SELECT id_kriteria, nama_kriteria, jenis_kriteria, bobot
	FROM tb_kriteria ORDER BY id_kriteria ASC');
$query->execute();
$query->setFetchMode(PDO::FETCH_ASSOC);
$kriterias = $query->fetchAll();

/* ---------------------------------------------
 * Fetch semua kambing (alternatif)
 * ------------------------------------------- */
$query2 = $pdo->prepare('SELECT id_alternatif, no_alternatif, nama_alternatif FROM tb_alternatif');
$query2->execute();
$query2->setFetchMode(PDO::FETCH_ASSOC);
$kambings = $query2->fetchAll();

array_unshift($kambings, array("id_alternatif" => 0, "no_alternatif" => "A0", "nama_alternatif" => ""));

/* >>> STEP 1 ===================================
 * Matrix Keputusan (X)
 * ------------------------------------------- */
$matriks_x = array();
$x_0 = array();
$list_kriteria = array();
foreach ($kriterias as $kriteria) :
	$list_kriteria[$kriteria['id_kriteria']] = $kriteria;
	foreach ($kambings as $kambing) :

		$id_kambing = $kambing['id_alternatif'];
		$id_kriteria = $kriteria['id_kriteria'];

		// Fetch nilai dari db
		$query3 = $pdo->prepare('SELECT nilai FROM tb_nilai
			WHERE id_alternatif = :id_alternatif AND id_kriteria = :id_kriteria');
		$query3->execute(array(
			'id_alternatif' => $id_kambing,
			'id_kriteria' => $id_kriteria,
		));
		$query3->setFetchMode(PDO::FETCH_ASSOC);
		if ($id_kambing == 0) {
			// Jika ada nilai kriterianya
			$matriks_x[$id_kriteria][$id_kambing] = 0;
		} else if ($nilai_kambing = $query3->fetch()) {
			$matriks_x[$id_kriteria][$id_kambing] = $nilai_kambing['nilai'];
		} else {
			$matriks_x[$id_kriteria][$id_kambing] = 0;
		}
	endforeach;
endforeach;
$x_0 = array();
$tipe = $list_kriteria[$id_kriteria]['jenis_kriteria'];
foreach ($matriks_x as $id_kriteria => $nilai_kambings) {
	if ($tipe == 'benefit') {
		$x_0[$id_kriteria] = max[$nilai_kambings] / 1;
	} elseif ($tipe == 'cost') {
		$x_0[$id_kriteria] = min[$nilai_kambings] / 1;
	}
}

foreach ($kriterias as $kriteria) :
	$i = 1;
	foreach ($kambings as $kambing) :
		$id_kambing = $kambing['id_alternatif'];
		$id_kriteria = $kriteria['id_kriteria'];
		if ($id_kambing == 0)
			continue;
		if ($list_kriteria[$id_kriteria]['jenis_kriteria'] == 'cost') {
			if ($matriks_x[$id_kriteria][0] == 0) {
				$matriks_x[$id_kriteria][0] = $matriks_x[$id_kriteria][$id_kambing];
			}
			if ($matriks_x[$id_kriteria][0] > $matriks_x[$id_kriteria][$id_kambing]) {
				$matriks_x[$id_kriteria][0] = $matriks_x[$id_kriteria][$id_kambing];
			}
		} else {
			if ($matriks_x[$id_kriteria][0] == 0) {
				$matriks_x[$id_kriteria][0] = $matriks_x[$id_kriteria][$id_kambing];
			}
			if ($matriks_x[$id_kriteria][0] < $matriks_x[$id_kriteria][$id_kambing]) {
				$matriks_x[$id_kriteria][0] = $matriks_x[$id_kriteria][$id_kambing];
			}
		}
		$i++;
	endforeach;
endforeach;

// array_unshift($matriks_x, $c_0);
/* >>> STEP 2 ===================================
 * Matriks Ternormalisasi (R)
 * ------------------------------------------- */
$sum_j = array();
foreach ($matriks_x as $id_kriteria => $xi) {
	$sum_j[$id_kriteria] = 0;
	$sum_j[$id_kriteria] = array_sum($xi);
}
$matriks_r = array();
foreach ($matriks_x as $id_kriteria => $xi) {
	$tipe = $list_kriteria[$id_kriteria]['jenis_kriteria'];
	foreach ($xi as $id_kambing => $xij) {
		if ($tipe == 'benefit') {
			$nilai_normal = $xij / $sum_j[$id_kriteria];
		} elseif ($tipe == 'cost') {
			$nilai_normal = (1 / $xij) / $sum_j[$id_kriteria];
		}
		$matriks_r[$id_kriteria][$id_kambing] = $nilai_normal;
	}
}
/* >>> STEP 3 ================================
 * Matriks Normalisasi Terbobot (D)
 * ------------------------------------------- */
$matriks_d = array();
foreach ($matriks_r as $id_kriteria => $ri) {
	$bobot = $list_kriteria[$id_kriteria]['bobot'];
	foreach ($ri as $id_kambing => $rij) {
		$normal_bobot = $rij * $bobot;
		$matriks_d[$id_kriteria][$id_kambing] = $normal_bobot;
	}
}

/* >>> STEP 4 ================================
 * Menghitung Nilai Optimum dan Utilitas
 * ------------------------------------------- */
/* >>> STEP 5 ================================
 * Perangkingan
 * ------------------------------------------- */
$ranks = array();
$tmp = 0;
foreach ($kambings as $kambing) :
	$utilitas = 0;
	$optimum[$kambing['id_alternatif']] = 0;
		$id_kambing = $kambing['id_alternatif'];
		$id_kriteria = $kriteria['id_kriteria'];
		
		$tmp = $optimum[$kambing['id_alternatif']==1];
		$utilitas = $utilitas + ($optimum[$kambing['id_alternatif']] / $tmp);

	$ranks[$kambing['id_alternatif']]['id_alternatif'] = $kambing['id_alternatif'];
	$ranks[$kambing['id_alternatif']]['no_alternatif'] = $kambing['no_alternatif'];
	$ranks[$kambing['id_alternatif']]['nama_alternatif'] = $kambing['nama_alternatif'];
	$ranks[$kambing['id_alternatif']]['nilai'] = $utilitas;

endforeach;

?>

<div class="main-content-row">
	<div class="container clearfix">

		<div class="main-content main-content-full the-content">

			<h1><?php echo $judul_page; ?></h1>


			<!-- STEP 1. Matriks Keputusan(X) ==================== -->
			<h3>Step 1: Matriks Keputusan (X)</h3>
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
							foreach ($kriterias as $kriteria) :
								$id_kambing = $kambing['id_alternatif'];
								$id_kriteria = $kriteria['id_kriteria'];
								echo '<td>';
								echo $matriks_x[$id_kriteria][$id_kambing];
								echo '</td>';
							endforeach;
							?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<!-- STEP 2. Matriks Ternormalisasi ==================== -->
			<h3>Step 2: Matriks Ternormalisasi (R)</h3>
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
							foreach ($kriterias as $kriteria) :
								$id_kambing = $kambing['id_alternatif'];
								$id_kriteria = $kriteria['id_kriteria'];
								echo '<td>';
								echo round($matriks_r[$id_kriteria][$id_kambing], $digit);
								echo '</td>';
							endforeach;
							?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<!-- Step 3: Matriks Normalisasi Terbobot (D) ==================== -->
			<h3>Step 3: Matriks Normalisasi Terbobot (D)</h3>
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
							foreach ($kriterias as $kriteria) :
								$id_kambing = $kambing['id_alternatif'];
								$id_kriteria = $kriteria['id_kriteria'];
								echo '<td>';
								echo round($matriks_d[$id_kriteria][$id_kambing], $digit);
								echo '</td>';
							endforeach;
							?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<!-- Step 4: Nilai Optimum dan Utilitas ==================== -->
			<h3>Step 4: Menghitung Nilai Optimum dan Utilitas</h3>
			<table class="pure-table pure-table-striped">
				<thead>
					<tr class="super-top">
						<th rowspan="2" class="super-top-left">Alternatif</th>
						<th colspan=7>Kriteria</th>

					</tr>
					<tr>
						<?php foreach ($kriterias as $kriteria) : ?>
							<th><?php echo $kriteria['nama_kriteria']; ?>
							<?php endforeach; ?></th>
							<th>Optimum</th>
							<th>Utilitas</th>
					</tr>
				</thead>
				<tbody>
					<?php $tmp = 0; $utilitas=array();foreach ($kambings as $kambing) : ?>
						<tr>
							<?php $optimum[$kambing['id_alternatif']] = 0; ?>
							<td><?php echo $kambing['no_alternatif']; ?></td>
							<?php
							foreach ($kriterias as $kriteria) :
								$id_kambing = $kambing['id_alternatif'];
								$id_kriteria = $kriteria['id_kriteria'];
								echo '<td>';
								echo round($matriks_d[$id_kriteria][$id_kambing], $digit);
								$optimum[$kambing['id_alternatif']] += round($matriks_d[$id_kriteria][$id_kambing], $digit);
								echo '</td>';
							endforeach;
							?>
							<td>
								<?php echo $optimum[$kambing['id_alternatif']]; ?>
							</td>
							<td>
								<?php if($tmp != 0) : ?>
									<?php echo round(($optimum[$kambing['id_alternatif']] / $tmp), $digit) ; ?>
								<?php endif; ?>
								<?php if($tmp == 0) : ?>
									<?php echo "" ; ?>
								<?php endif; ?>

								<?php $tmp = $optimum[$kambing['id_alternatif']==1]; ?>
								<?php $utilitas = $optimum[$kambing['id_alternatif']] / $tmp;?>
								<?php $ranks[$kambing['id_alternatif']]['nilai'] = $utilitas;?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>


			<!-- Step 5: Perangkingan ==================== -->
			<?php
			$sorted_ranks = $ranks;
			// Sorting
			if (function_exists('array_multisort')) :
				$no_kalung = array();
				$nilai = array();
				foreach ($sorted_ranks as $key => $row) {
					$no_kalung[$key]  = $row['no_alternatif'];
					$nilai[$key] = $row['nilai'];
				}
				array_multisort($nilai, SORT_DESC, $no_kalung, SORT_ASC, $sorted_ranks);
			endif;
			?>
			<h3>Step 5: Hasil Akhir Perangkingan </h3>
			<table class="pure-table pure-table-striped">
				<thead>
					<tr>
						<th class="super-top-left">Ranking</th>
						<th>Alternatif</th>
						<th>Nama Alternatif</th>
						<th>Nilai Utilitas</th>
					</tr>
				</thead>
				<tbody>
					<?php $no = 0;
					foreach ($sorted_ranks as $i => $kambing) : ?>
						<tr>
							<td><?php echo $no; ?></td>
							<td><?php echo $kambing['no_alternatif']; ?></td>
							<td><?php echo $kambing['nama_alternatif']; ?></td>
							<td><?php echo round($kambing['nilai'], $digit); ?></td>
							<?php
							if ($i < 2) {
								$alternatif = $kambing['no_alternatif'];

								$query4 = $pdo->prepare('SELECT * FROM tb_alternatif WHERE no_alternatif = :no_alternatif');
								$query4->execute(array('no_alternatif' => $alternatif));
								$terpilih = $query4->fetch();
								$nilai_terpilih = $kambing['nilai'];
							}
							?>
						</tr>
					<?php $no++;
					endforeach; ?>
				</tbody>
			</table>

			<?php if ($sorted_ranks) : ?>
				<table class="pure-table pure-table-striped">
					<thead>
						<tr>
							<th>Kesimpulan</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								Dari hasil perhitungan yang dilakukan menggunakan metode ARAS, Media Pembelajaran Online tertinggi adalah <?php echo $terpilih['nama_alternatif']; ?> dengan nilai <?php echo $nilai_terpilih; ?>
							</td>
						</tr>
					</tbody>
				</table>
			<?php endif; ?>

		</div>

	</div><!-- .container -->
</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');
