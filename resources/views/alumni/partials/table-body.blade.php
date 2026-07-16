@forelse($alumni as $key => $item)
    <tr>
        <td>{{ $alumni->firstItem() + $key }}</td>
        <td class="fw-600">{{ $item->nama_lengkap }}</td>
        <td>{{ $item->tahun_lulus }}</td>
        <td>
            @if($item->status === 'Melanjutkan Studi')
                <span class="badge bg-primary">{{ $item->status }}</span>
            @else
                <span class="badge bg-success">{{ $item->status }}</span>
            @endif
        </td>
        <td>
            @if($item->status === 'Melanjutkan Studi')
                <small class="text-soft">{{ $item->jenis_studi }} ({{ $item->jalur_penerimaan }})</small>
            @else
                <small class="text-soft">{{ $item->jenis_pekerjaan }}</small>
            @endif
            <br>
            <small class="text-soft">{{ Str::limit($item->keterangan ?? '-', 50) }}</small>
        </td>
        <td class="text-center">
            <button class="btn btn-sm btn-soft d-inline-flex align-items-center gap-1" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalAlumni"
                    onclick="editAlumni({{ json_encode($item) }})" 
                    type="button" 
                    title="Edit">
                <span class="material-symbols-outlined fs-6">edit</span>
            </button>
            <form action="{{ route('alumni.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-soft d-inline-flex align-items-center gap-1 text-danger" type="submit" title="Hapus">
                    <span class="material-symbols-outlined fs-6">delete</span>
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center text-soft py-5">
            <div>
                <span class="material-symbols-outlined" style="font-size: 3rem; opacity: 0.3;">person_off</span>
                <p class="mt-3 mb-0">Belum ada data alumni</p>
            </div>
        </td>
    </tr>
@endforelse
