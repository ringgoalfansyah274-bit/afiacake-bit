<table class="table">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>WhatsApp</th>
            <th>Tanggal Lahir</th>
            <th>Kode Voucher</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($members as $m): ?>
        <tr>
            <td><?= $m['nama'] ?></td>
            <td><?= $m['email'] ?></td>
            <td><?= $m['whatsapp'] ?></td>
            <td><?= date('d/m/Y', strtotime($m['tanggal_lahir'])) ?></td>
            <td><strong><?= $m['kode_voucher'] ?></strong></td>
            <td>
                <a href="https://wa.me/<?= $m['whatsapp'] ?>" target="_blank">Chat</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>