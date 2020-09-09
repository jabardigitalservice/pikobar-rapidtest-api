<table>
    <tr>
        <th>NO</th>
        <th>INSTANSI_PENGIRIM</th>
        <th>FASYANKES/DINKES</th>
        <th>KODE SAMPLE</th>
        <th>KODE REGISTRASI</th>
        <th>STATUS_SASARAN</th>
        <th>PEKERJAAN/ KATEGORI</th>
        <th>NAMA_PASIEN</th>
        <th>NIK</th>
        <th>NOMOR TELEPON</th>
        <th>JENIS_KELAMIN</th>
        <th>TEMPAT_LAHIR</th>
        <th>TANGGAL_LAHIR</th>
        <th>KOTA</th>
        <th>KECAMATAN</th>
        <th>KELUARAHAN</th>
        <th>ALAMAT</th>
        <th>KEWARGANEGARAAN</th>
        <th>KUNJUNGAN</th>
        <th>GEJALA</th>
        <th>TANGGAL_MUNCUL_GEJALA</th>
        <th>PENYAKIT PENYERTA</th>
        <th>RIWAYAT PERJALANAN</th>
        <th>APAKAH_PERNAH_KONTAK</th>
        <th>JIKA_IYA_TANGGAL_KONTAK</th>
        <th>TANGGAL_ACARA</th>
        <th>JAM_ACARA</th>
        <th>TEMPAT_ACARA</th>
        <th>KETERANGAN</th>
        <th>HASIL_TEST</th>
        <th>NILAI_CT</th>
    </tr>
    @foreach($rdtinvitations as $rdtinvitation)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $rdtinvitation->attend_location }}</td>
        <td>{{ $rdtinvitation->event->host_name }}</td>
        <td>{{ $rdtinvitation->lab_code_sample}}</td>
        <td>{{ $rdtinvitation->registration_code }}</td>
        <td>{{ $rdtinvitation->applicant->person_status }}</td>
        <td>PEKERJAAN/ Kategori</td>
        <td>{{ $rdtinvitation->applicant->name}}</td>
        <td>{{ $rdtinvitation->applicant->nik }}</td>
        <td>{{ $rdtinvitation->applicant->phone_number}}</td>
        <td>{{ $rdtinvitation->applicant->gender==1?'Laki-Laki':'Perempuan' }}</td>
        <td>Tempat lahir</td>
        <td>{{ $rdtinvitation->applicant->birth_date}}</td>
        <td>{{ $rdtinvitation->applicant->city->name}}</td>
        <td>{{ $rdtinvitation->applicant->city->district}}</td>
        <td>-</td>
        <td>{{ $rdtinvitation->applicant->address}}</td>
        <td>WNI</td>
        <td>-</td>
        <td>-</td>
        <td>-</td>
        <td>{{ $rdtinvitation->applicant->congenital_disease }}</td>
        <td>{{ $rdtinvitation->applicant->city_visited}}</td>
        <td>{{ $rdtinvitation->applicant->have_interacted }}</td>
        <td>-</td>
        <td>{{ $rdtinvitation->event->start_at }}</td>
        <td>{{ $rdtinvitation->event->end_at }}</td>
        <td>{{ $rdtinvitation->event->event_location }}</td>
        <td>-</td>
        <td>{{ $rdtinvitation->lab_result_type }}</td>
        <td>-</td>
    </tr>
    @endforeach
</table>