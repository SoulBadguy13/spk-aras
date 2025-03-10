<?php require_once('includes/init.php'); ?>

<?php
$judul_page = 'List Kriteria';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-kriteria.php'); ?>
	
		<div class="main-content the-content">
			
			<?php
			$status = isset($_GET['status']) ? $_GET['status'] : '';
			$msg = '';
			switch($status):
				case 'sukses-baru':
					$msg = 'Kriteria baru berhasil dibuat';
					break;
				case 'sukses-hapus':
					$msg = 'Kriteria behasil dihapus';
					break;
				case 'sukses-edit':
					$msg = 'Kriteria behasil diedit';
					break;
			endswitch;
			
			if($msg):
				echo '<div class="msg-box msg-box-full">';
				echo '<p><span class="fa fa-bullhorn"></span> &nbsp; '.$msg.'</p>';
				echo '</div>';
			endif;
			?>
			
			<h1>List Kriteria</h1>
			
			<?php
			$query = $pdo->prepare('SELECT * FROM tb_kriteria ORDER BY id_kriteria ASC');			
			$query->execute();
			// menampilkan berupa nama field
			$query->setFetchMode(PDO::FETCH_ASSOC);
			
			if($query->rowCount() > 0):
			?>
			
			<table class="pure-table pure-table-striped">
				<thead>
					<tr>
						<th>Nama Kriteria</th>
						<th>Jenis Kriteria</th>
						<th>Bobot</th>
						<th>Detail</th>
						<th>Edit</th>
						<th>Hapus</th>
					</tr>
				</thead>
				<tbody>
					<?php while($hasil = $query->fetch()): ?>
						<tr>
							<td><?php echo $hasil['nama_kriteria']; ?></td>
							<td>
							<?php
							if($hasil['jenis_kriteria'] == 'benefit') {
								echo 'Benefit';
							} elseif($hasil['jenis_kriteria'] == 'cost') {
								echo 'Cost';
							}
							?>
							</td>
							<td><?php echo $hasil['bobot']; ?></td>																					
							<td><a href="single-kriteria.php?id=<?php echo $hasil['id_kriteria']; ?>"><span class="fa fa-eye"></span> Detail</a></td>
							<td><a href="edit-kriteria.php?id=<?php echo $hasil['id_kriteria']; ?>"><span class="fa fa-pencil"></span> Edit</a></td>
							<td><a href="hapus-kriteria.php?id=<?php echo $hasil['id_kriteria']; ?>" class="red yaqin-hapus"><span class="fa fa-times"></span> Hapus</a></td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>

			<?php else: ?>
				<p>Maaf, belum ada data untuk kriteria.</p>
			<?php endif; ?>
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->

<?php
require_once('template-parts/footer.php');