<!DOCTYPE html>
<html lang="en">
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<!-- Meta, title, CSS, favicons, etc. -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="https://fonts.gstatic.com/s/i/materialicons/school/v6/24px.svg" type="image/svg+xml">
	<title>SMP 3 Muhammadiyah | Buat Qr Code Kehadiran</title>

	<!-- Bootstrap -->
    <link href="{{asset('lte/vendors/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{asset('lte/vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{asset('lte/vendors/nprogress/nprogress.css')}}" rel="stylesheet">
    <!-- iCheck -->
    <link href="{{asset('lte/vendors/iCheck/skins/flat/green.css')}}" rel="stylesheet">
	
    <!-- bootstrap-progressbar -->
    <link href="{{asset('lte/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css')}}" rel="stylesheet">
    <!-- JQVMap -->
    <link href="{{asset('lte/vendors/jqvmap/dist/jqvmap.min.css')}}" rel="stylesheet"/>
    <!-- bootstrap-daterangepicker -->
    <link href="{{asset('lte/vendors/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="{{asset('lte/build/css/custom.min.css')}}" rel="stylesheet">
	<!-- bootstrap-wysiwyg -->
	<link href="{{asset('lte/vendors/google-code-prettify/bin/prettify.min.css')}}" rel="stylesheet">
	<!-- Select2 -->
	<link href="{{asset('lte/vendors/select2/dist/css/select2.min.css')}}" rel="stylesheet">
	<!-- Switchery -->
	<link href="{{asset('lte/vendors/switchery/dist/switchery.min.css')}}" rel="stylesheet">
	<!-- starrr -->
	<link href="{{asset('lte/vendors/starrr/dist/starrr.css')}}" rel="stylesheet">
  <!-- Custom Theme Style -->
  <link href="{{asset('lte/build/css/custom.css')}}" rel="stylesheet">
	

</head>

<body class="nav-md">
  
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href={{route('dashboard.admin.index')}} class="site_title"><i class="fa fa-server"></i><span> Administrator</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
                <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('images/default-image.jpg') }}" alt="Photo User" class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>Welcome,</span>
                <h2>{{ $user->name }}</h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>General</h3>
                
                <ul class="nav side-menu">
                
                
                  <li><a href="{{route('dashboard.admin.index')}}"><i class="fa fa-home"></i> Home</a></li>
                  <li><a><i class="fa fa-table"></i> Kehadiran <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      
                    <li><a href="{{route('dashboard.tables_attend.table_teacher')}}">Guru</a></li>
                      <li><a href="{{route('dashboard.tables_attend.table_student')}}">Siswa</a></li>
                    </ul>
                  </li>
                  <li><a href="{{route('dashboard.attendance.create')}}"><i class="fa fa-plus"></i>Buat Qr Code Kehadiran</a>
                  </li>
                  <li><a href="{{route('dashboard.course.index')}}"><i class="fa fa-list"></i>Daftar Pelajaran</a>
                  </li>
                  <li><a href="{{route('dashboard.classroom.index')}}"><i class="fa fa-list-alt"></i>Daftar Kelas</a>
                  </li>
                  <li><a href="{{route('dashboard.schedule.index')}}"><i class="fa fa-clipboard"></i> Jadwal Pelajaran </a></li>
                  <li><a href="{{route('dashboard.each_schedule.index')}}"><i class="fa fa-clipboard"></i> Jadwal Guru & Siswa </a></li>
                  <li><a><i class="fa fa-credit-card"></i> Daftar Pengguna <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="{{route('dashboard.admin_list.index')}}">Daftar Admin</a></li>
                      <li><a href="{{route('dashboard.teacher_list.index')}}">Daftar Guru</a></li>
                      <li><a href="{{route('dashboard.student_list.index')}}">Daftar Siswa</a></li>
                    </ul>
                  </li>
                  
                  
                </ul>
              </div>



              
              

            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
              
              <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" data-toggle="tooltip" data-placement="top" title="Logout">
                  <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>
            <!-- /menu footer buttons -->
          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
              <nav class="nav navbar-nav">
              <ul class=" navbar-right">
                <li class="nav-item dropdown open" style="padding-left: 15px;">
                  <a href="javascript:;" class="user-profile dropdown-toggle" aria-haspopup="true" id="navbarDropdown" data-toggle="dropdown" aria-expanded="false">
                    <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('images/default-image.jpg') }}" alt="">{{$user->name}}
                    
                    
                  </a>
                  <div class="dropdown-menu dropdown-usermenu pull-right" aria-labelledby="navbarDropdown">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                      @csrf
                    </form>
                    
                    <a class="dropdown-item"  href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out pull-right"></i> Log Out</a>
                  </div>
                </li>

                <li role="presentation" class="nav-item dropdown open">
                  
                 
                </li>
              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->

		<!-- page content -->
		<div class="right_col" role="main">
			<div class="">
				<div class="page-title">
					<div class="title_left">
						<h3>Buat Qr Code Kehadiran Guru dan Siswa</h3>
					</div>

					
				</div>
				<div class="clearfix"></div>
				<div class="row">
					<div class="col-md-12 col-sm-12 ">
						<div class="x_panel">
							<div class="x_title">
								<h2>Buat Qr Code Kehadiran Guru dan Siswa<small>pada form di bawah</small></h2>
								<ul class="nav navbar-right panel_toolbox">
									<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
									</li>
									
									<li><a class="close-link"><i class="fa fa-close"></i></a>
									</li>
								</ul>
								<div class="clearfix"></div>
							</div>
							<div class="x_content">
								<br />

								<form action="{{ route('dashboard.attendance.store') }}" method="POST">
                  @csrf
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="course_id">Nama Pelajaran <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 ">
                            <select id="course" name="course_id" required="required" class="form-control">
                                <option value="">Pilih Pelajaran</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="classroom_id">Kelas <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 ">
                            <select id="classroom" name="classroom_id" required="required" class="form-control">
                                <option value="">Kelas</option>
                                @foreach($classrooms as $classroom)
                                    <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                
                    <div class="item form-group">
                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="lesson_time">Waktu Pelajaran <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6 ">
                            <input type="datetime-local" name="lesson_time" id="lesson_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="ln_solid"></div>
                    <div class="item form-group">
                        <div class="col-md-6 col-sm-6 offset-md-3">
                            <button class="btn btn-primary" type="button" onclick="window.history.back()">Cancel</button>
                            <button class="btn btn-primary" type="reset">Reset</button>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>

                <div id="qrcode-container" class="text-center mt-4" style="display: none;">
                  <h4>Generated QR Code</h4>
                  <div id="qrcode"></div>
              </div>
              
							</div>
						</div>
					</div>
				</div>

				


				
			</div>
		</div>
		<!-- /page content -->
		
		
		
		


        <!-- footer content -->
        <footer>
          <div class="pull-right">
            SMP 3 Muhammadiyah
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="{{asset('lte/vendors/jquery/dist/jquery.min.js')}}"></script>
    <!-- Bootstrap -->
    <script src="{{asset('lte/vendors/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
    <!-- FastClick -->
    <script src="{{asset('lte/vendors/fastclick/lib/fastclick.js')}}"></script>
    <!-- NProgress -->
    <script src="{{asset('lte/vendors/nprogress/nprogress.js')}}"></script>
    <!-- Chart.js -->
    <script src="{{asset('lte/vendors/Chart.js/dist/Chart.min.js')}}"></script>
    <!-- gauge.js -->
    <script src="{{asset('lte/vendors/gauge.js/dist/gauge.min.js')}}"></script>
    <!-- bootstrap-progressbar -->
    <script src="{{asset('lte/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js')}}"></script>
    <!-- iCheck -->
    <script src="{{asset('lte/vendors/iCheck/icheck.min.js')}}"></script>
	<!-- bootstrap-daterangepicker -->
	<script src="{{asset('lte/vendors/moment/min/moment.min.js')}}"></script>
	<script src="{{asset('lte/vendors/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
	<!-- bootstrap-wysiwyg -->
	<script src="{{asset('lte/vendors/bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js')}}"></script>
	<script src="{{asset('lte/vendors/jquery.hotkeys/jquery.hotkeys.js')}}"></script>
	<script src="{{asset('lte/vendors/google-code-prettify/src/prettify.js')}}"></script>
	<!-- jQuery Tags Input -->
	<script src="{{asset('lte/vendors/jquery.tagsinput/src/jquery.tagsinput.js')}}"></script>
	<!-- Switchery -->
	<script src="{{asset('lte/vendors/switchery/dist/switchery.min.js')}}"></script>
	<!-- Select2 -->
	<script src="{{asset('lte/vendors/select2/dist/js/select2.full.min.js')}}"></script>
	<!-- Parsley -->
	<script src="{{asset('lte/vendors/parsleyjs/dist/parsley.min.js')}}"></script>
	<!-- Autosize -->
	<script src="{{asset('lte/vendors/autosize/dist/autosize.min.js')}}"></script>
	<!-- jQuery autocomplete -->
	<script src="{{asset('lte/vendors/devbridge-autocomplete/dist/jquery.autocomplete.min.js')}}"></script>
	<!-- starrr -->
	<script src="{{asset('lte/vendors/starrr/dist/starrr.js')}}"></script>
	<!-- Custom Theme Scripts -->

  <!-- 1. Impor Library Tambahan untuk QR Code -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode/1.5.1/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>

    <!-- 2. Script Logika QR Code -->
    <script>
      let html5QrCode;

      function initializeScanner() {
          // Pengecekan jika library QRCode dari CDN belum siap
          if (typeof QRCode === "undefined") {
              console.warn("Library QRCode belum siap, mencoba ulang dalam 500ms...");
              setTimeout(initializeScanner, 500);
              return;
          }

          // Inisialisasi Scanner (jika elemen #reader ada di halaman)
          const readerElement = document.getElementById("reader");
          if (readerElement && typeof Html5Qrcode !== "undefined") {
              html5QrCode = new Html5Qrcode("reader");

              html5QrCode.start(
                  { facingMode: "environment" },
                  { fps: 10, qrbox: { width: 250, height: 250 } },
                  onScanSuccess,
                  onScanError
              ).catch(err => {
                  console.error('Error starting QR Code scanner:', err);
              });
          }

          // Jalankan Generator QR Code
          generateQRCode('25 2024-10-09T13:45');
      }

      function generateQRCode(textData) {
          const qrContainer = document.getElementById('qrcode');
          if (!qrContainer) return;

          const qrParentContainer = document.getElementById('qrcode-container');
          if (qrParentContainer) {
              qrParentContainer.style.display = 'block';
          }

          if (typeof QRCode !== "undefined" && typeof QRCode.toCanvas === "function") {
              let canvas = qrContainer.querySelector('canvas');
              if (!canvas) {
                  canvas = document.createElement('canvas');
                  qrContainer.innerHTML = '';
                  qrContainer.appendChild(canvas);
              }

              QRCode.toCanvas(canvas, textData, function (error) {
                  if (error) {
                      console.error('QR Generator Error:', error);
                  } else {
                      console.log('QR code successfully generated!');
                  }
              });
          } else {
              console.error("Library QRCode (node-qrcode) belum dimuat.");
          }
      }

      function onScanSuccess(decodedText) {
          console.log('Scanned QR code data:', decodedText);

          const qr_code_id = extractQrCodeId(decodedText);
          console.log('Extracted QR code ID:', qr_code_id);

          if (qr_code_id === null) {
              alert('Error: Invalid QR code format.');
              return;
          }

          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

          fetch('/dashboard/scan-qr', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': csrfToken
              },
              body: JSON.stringify({
                  qr_code_id: qr_code_id,
              })
          })
          .then(async response => {
              const data = await response.json();
              if (!response.ok) {
                  throw data;
              }
              return data;
          })
          .then(data => {
              console.log('Server response:', data);
              alert(data.success || 'Data successfully recorded!');
          })
          .catch(error => {
              console.error('Error:', error);
              alert(error.error || 'Failed to record data. Please try again.');
          });
      }

      function extractQrCodeId(decodedText) {
          const parts = decodedText.split(' ');
          const id = parseInt(parts[0], 10);

          if (isNaN(id)) {
              console.error('Invalid QR code ID:', decodedText);
              return null;
          }
          return id;
      }

      function onScanError(errorMessage) {
          // Callback scan error
      }

      document.addEventListener('DOMContentLoaded', initializeScanner);
    </script>
  </body>
</html>
  </body>

</html>
