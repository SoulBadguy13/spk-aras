<?php require_once('includes/init.php'); ?>

<?php
$errors = array();
$sukses = false;

$nama_kriteria = (isset($_POST['nama_kriteria'])) ? trim($_POST['nama_kriteria']) : '';
$jenis_kriteria = (isset($_POST['jenis_kriteria'])) ? trim($_POST['jenis_kriteria']) : '';
$bobot = (isset($_POST['bobot'])) ? trim($_POST['bobot']) : '';
$jenis_nilai = (isset($_POST['jenis_nilai'])) ? trim($_POST['jenis_nilai']) : 0;
$pilihan = (isset($_POST['pilihan'])) ? $_POST['pilihan'] : '';

if(isset($_POST['submit'])):	
	
	// Validasi Nama Kriteria
	if(!$nama_kriteria) {
		$errors[] = 'Nama kriteria tidak boleh kosong';
	}		
	// Validasi Tipe
	if(!$jenis_kriteria) {
		$errors[] = 'Type kriteria tidak boleh kosong';
	}
	// Validasi Bobot
	if(!$bobot) {
		$errors[] = 'Bobot kriteria tidak boleh kosong';
	}	
	
	// Jika lolos validasi lakukan hal di bawah ini
	if(empty($errors)):
		
		$handle = $pdo->prepare('INSERT INTO tb_kriteria (nama_kriteria, jenis_kriteria, bobot, cara_penilaian) VALUES (:nama_kriteria, :jenis_kriteria, :bobot, :jenis_nilai)');
		$handle->execute( array(
			'nama_kriteria' => $nama_kriteria,
			'jenis_kriteria' => $jenis_kriteria,
			'bobot' => $bobot,
			'jenis_nilai' => $jenis_nilai			
		) );
		$id_kriteria = $pdo->lastInsertId();
		
		if($id_kriteria && $jenis_nilai == 1 && !empty($pilihan)): foreach($pilihan as $pil):
			
			$keterangan = (isset($pil['keterangan'])) ? trim($pil['keterangan']) : '';
			$nilai = (isset($pil['nilai'])) ? floatval($pil['nilai']) : '';
						
			
			if($keterangan != '' && ($nilai >= 0)):
				
				$prepare_query = 'INSERT INTO tb_parameter (keterangan, id_kriteria, nilai) VALUES  (:keterangan, :id_kriteria, :nilai)';
				$data = array(
					'keterangan' => $keterangan,
					'id_kriteria' => $id_kriteria,
					'nilai' => $nilai,	
				);		
				$handle = $pdo->prepare($prepare_query);		
				$sukses = $handle->execute($data);				
				
			endif;		
		endforeach; endif;
		
		redirect_to('list-kriteria.php?status=sukses-baru');		
	
	endif;

endif;
?>

<?php
$judul_page = 'Tambah kriteria';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-kriteria.php'); ?>
	
		<div class="main-content the-content">
			<h1>Tambah Kriteria</h1>
			
			<?php if(!empty($errors)): ?>
			
				<div class="msg-box warning-box">
					<p><strong>Error:</strong></p>
					<ul>
						<?php foreach($errors as $error): ?>
							<li><?php echo $error; ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
				
			<?php endif; ?>			
			
				<form action="tambah-kriteria.php" method="post">
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
							<option value="0">Inputan Langsung</option>
							<option value="1">Menggunakan Pilihan Variabel</option>						
						</select>
					</div>
					
					<div class="field-wrap list-var clearfix sembunyikan">					
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
							</tbody>
						</table>
						<div class="align-right">
							<a href="#" class="button tambah-pilihan">Tambah Pilihan</a>
						</div>
					</div>
					
					<div class="field-wrap clearfix">
						<button type="submit" name="submit" value="submit" class="button">Tambah Kriteria</button>
					</div>
				</form>
				
			<?php //endif; ?>			
			
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');