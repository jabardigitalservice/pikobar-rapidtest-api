<style type="text/css">
  body {
    font-family: Arial, sans-serif;
  }

  table {
    width: 100%;
    font-size: 8pt;
    border-collapse: collapse;
  }

  table, table th, table td {
    border: 1px solid black;
    padding: 5px;
  }

  table.detail, table.detail th, table.detail td {
    border: 0;
    padding: 10px;
    font-size: 12pt;
  }
</style>

<table>
  <thead>
  <tr>
    <th style="padding: 30px">
      <h1 style="font-size: 14pt; margin: 0;">Bukti Pendaftaran Tes Masif COVID-19</h1>
      <h2 style="font-size: 12pt; margin: 0; font-weight: normal">Pusat Informasi dan Koordinasi Jawa Barat (PIKOBAR)</h2>
    </th>
  </tr>
  </thead>
  <tbody>
  <tr>
    <td align="center" style="padding: 30px">
      <h1 style="font-size: 14pt; margin: 0; margin-top: 15px">Nama Peserta:</h1>
      <h1 style="font-size: 24pt; margin: 0; font-weight: normal">{{ $applicant->name }}</h1>
      <p><img src="data:image/png;base64, {{ base64_encode(QrCode::errorCorrection('H')->format('png')->merge('/storage/pikobar.png')->margin(0)->size(600)->generate($applicant->registration_code)) }}"></p>
      <h1 style="font-size: 14pt; margin: 0">Nomor Pendaftaran:</h1>
      <h1 style="font-size: 48pt; margin: 0">{{ $applicant->registration_code }}</h1>
    </td>
  </tr>
  <tr>
    <td>
      <table class="detail">
        @if ($applicant->pikobar_session_id)
          <tr>
            <th width="250px" align="right">Session ID</th>
            <td>{{ $applicant->pikobar_session_id }}</td>
          </tr>
        @endif
        <tr>
          <th width="250px" align="right">Nomor Pendaftaran</th>
          <td>{{ $applicant->registration_code }}</td>
        </tr>
        <tr>
          <th width="250px" align="right">Tanggal Pendaftaran</th>
          <td>{{ $applicant->registration_at->setTimezone('Asia/Jakarta') }}</td>
        </tr>
        <tr>
          <th width="250px" align="right">Nama Peserta</th>
          <td>{{ $applicant->name }}</td>
        </tr>
        <tr>
          <th width="250px" align="right">Kab/Kota Domisili</th>
          <td>{{ optional($applicant->city)->name }}</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td style="padding: 30px">
      <h2 style="margin: 5px 0; font-size: 16px">Info Penting</h2>

      @if ($applicant->status->isEqual(\App\Enums\RdtApplicantStatus::NEW()))
      <div style="background-color: #ffd4d4; padding: 10px 20px; margin-top: 20px; font-size: 11pt">
        <h3>Menunggu Verifikasi</h3>
        <p>Hanya yang sudah diverifikasi oleh Dinas Kesehatan Provinsi atau Dinas Kesehatan Kabupaten/Kota akan mendapatkan undangan untuk mengikuti tes masif COVID-19. Undangan akan dikirimkan melalui SMS, Whatsapp, dan Email yang digunakan untuk mendaftar.</p>
      </div>
      @endif

      <ol style="font-size: 11pt">
        <li><strong>Simpan Nomor Pendaftaran Anda.</strong><br/>
        Nomor Pendaftaran digunakan untuk mendapatkan undangan dan hasil test.</li>

        <li style="margin-top: 14px"><strong>Cek status pendaftaran/undangan Anda melalui website.</strong><br/>
          Buka <a href="https://tesmasif.pikobar.jabarprov.go.id">https://tesmasif.pikobar.jabarprov.go.id</a> dan masukkan Nomor Pendaftaran.</li>

        <li style="margin-top: 14px"><strong>Jangan membagikan nomor pendaftaran ini kepada orang lain.</strong><br/>
          Pastikan identitas Anda tidak digunakan orang lain.</li>

        <li style="margin-top: 14px"><strong>Tes Masif COVID-19 ini tidak dipungut biaya.</strong></li>

        <li style="margin-top: 14px"><strong>Unduh aplikasi PIKOBAR (Android/iOS).</strong><br/>
          Untuk mengakses informasi perkembangan terkini penanganan COVID-19 di Jawa Barat, unduh di <a href="https://bit.ly/PIKOBAR-V1">https://bit.ly/PIKOBAR-V1</a></li>

        <li style="margin-top: 14px"><strong>Informasi dan pertanyaan lebih lanjut hubungi Pusat Bantuan PIKOBAR.</strong><br/>
          Hotline <a href="https://api.whatsapp.com/send?phone=628112093306">08112093306</a> atau Gugus Tugas/Dinas Kesehatan Kota/Kabupaten/Provinsi setempat.</li>
      </ol>
    </td>
  </tr>
  </tbody>
</table>
