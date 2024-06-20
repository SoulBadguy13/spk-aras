<?php require_once('includes/init.php'); ?>

<?php
$ada_error = false;
$result = '';

$id_kriteria = (isset($_GET['id'])) ? trim($_GET['id']) : '';

if(!$id_kriteria) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = $pdo->prepare('SELECT * FROM tb_kriteria WHERE tb_kriteria.id_kriteria = :id_kriteria');
	$query->execute(array('id_kriteria' => $id_kriteria));
	$result = $query->fetch();
	
	if(empty($result)) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	}
}
?>

<?php
$judul_page = 'Detail Kriteria';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-kriteria.php'); ?>
	
		<div class="main-content the-content">
			<h1><?php echo $judul_page; ?></h1>
			
			<?php if($ada_error): ?>
			
				<?php echo '<p>'.$ada_error.'</p>'; ?>
				
			<?php elseif(!empty($result)): ?>
			
				<h4>Nama Kriteria</h4>
				<p><?php echo $result['nama_kriteria']; ?></p>
				
				<h4>Jenis Kriteria</h4>
				<p><?php
				if($result['jenis_kriteria'] == 'benefit') {
					echo 'Benefit (Keuntungan)';
				} elseif($result['jenis_kriteria'] == 'cost') {
					echo 'Cost (Kerugian)';
				}
				?></p>
				
				<h4>Bobot Kriteria</h4>
				<p><?php echo $result['bobot']; ?></p>
				

				<h4>Cara Penilaian</h4>
				
				<p><?php
				if($result['cara_penilaian'] == 1) {
					echo 'Menggunakan Pilihan Variabel';
				} else {
					echo 'Inputan Langsung';
				}				
				?></p>
				
				<?php if($result['cara_penilaian'] == 1): ?>
					<h4>Parameter</h4>
						<table id="pilihan-var" class="pure-table pure-table-striped">
							<thead>
								<tr>
									<th>Nama Parameter</th>
									<th>Nilai Parameter</th>					
								</tr>
							</thead>
							<tbody>
								
								<?php
								$query = $pdo->prepare('SELECT * FROM tb_parameter WHERE id_kriteria = :id_kriteria ORDER BY nilai DESC');			
								$query->execute(array(
									'id_kriteria' => $result['id_kriteria']
								));
								// menampilkan berupa nama field
								$query->setFetchMode(PDO::FETCH_ASSOC);
								if($query->rowCount() > 0): while($hasile = $query->fetch()):
								?>								
									<tr>
										<td><?php echo $hasile['keterangan']; ?></td>							
										<td><?php echo $hasile['nilai']; ?></td>
									</tr>
								<?php endwhile; endif;?>
								
							</tbody>
						</table>
				<?php endif; ?>
				
				<p><a href="edit-kriteria.php?id=<?php echo $id_kriteria; ?>" class="button"><span class="fa fa-pencil"></span> Edit</a> &nbsp; <a href="hapus-kriteria.php?id=<?php echo $id_kriteria; ?>" class="button button-red yaqin-hapus"><span class="fa fa-times"></span> Hapus</a></p>
			
			
			<?php endif; ?>
			
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');