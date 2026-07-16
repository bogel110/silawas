@forelse($alumni as $key => $item)
    <tr>
        <td>{{ $alumni->firstItem() + $key }}</td>
        <td class="fw-600">{{ $item->nama_lengkap }}</td>
        <td>{{ $item->tahun_lulus }}</td>
        <td>
            @if($item->status === 'Melanjutkan Studi')
                <span class="badge bg-primary">Melanjutkan Studi</span>
            @else
                <span class="badge bg-success">Bekerja</span>
            @endif
        </td>
        <td>
            @if($item->status === 'Melanjutkan Studi')
                <small class="text-soft">
                    <strong>{{ $item->jenis_studi }}</strong> ({{ $item->jalur_penerimaan }})<br>
                    {{ Str::limit($item->keterangan ?? '-', 50) }}
                </small>
            @else
                <small class="text-soft">
                    <strong>{{ $item->jenis_pekerjaan }}</strong><br>
                    {{ Str::limit($item->keterangan ?? '-', 50) }}
                </small>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center text-soft py-5">
            <div>
                <span class="material-symbols-outlined" style="font-size: 3rem; opacity: 0.3;">person_off</span>
                <p class="mt-3 mb-0">Belum ada data alumni untuk sekolah ini</p>
            </div>
        </td>
    </tr>
@endforelse
