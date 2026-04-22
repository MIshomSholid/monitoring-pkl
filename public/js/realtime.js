(function () {
    if (typeof io === "undefined") {
        console.warn("Socket.IO not loaded");
        return;
    }

    const { userId, role } = window.REALTIME_CONFIG || {};
    const socket = io("https://accomplished-truth.up.railway.app", {
        transports: ["websocket"],
    });

    socket.on("connect", function () {
        socket.emit("join-user", {
            user_id: userId,
            role: role,
        });

        console.log("[Realtime] Connected as:", role);
    });

    socket.on("presensi.created", handlePresensiCreated);

    socket.on("presensi.validated", function (data) {
        console.log("[Realtime] Presensi validated:", data);

        const card = document.getElementById("presensi-card-" + data.id);
        if (card) {
            card.remove();
        }

        const row = document.getElementById("presensi-row-" + data.id);
        if (row) {
            row.remove();
        }
    });

    function handlePresensiCreated(data) {
        console.log("[Realtime] Presensi:", data);

        /* =============================
           CARD PAGE (VALIDASI PEMBIMBING)
        ============================= */

        const cardContainer = document.querySelector("#presensi-container");

        if (cardContainer) {
            if (document.getElementById("presensi-card-" + data.id)) return;

            const card = `
                <div id="presensi-card-${data.id}" 
                    class="bg-white p-6 rounded-md shadow border border-green-200">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 text-sm text-gray-700">

                        <p><span class="font-medium">Nama Siswa:</span> ${data.nama_siswa}</p>

                        <p><span class="font-medium">Tempat PKL:</span> 
                            ${data.nama_perusahaan ?? "-"}
                        </p>

                        <p><span class="font-medium">Jenis Presensi:</span> 
                            ${jenisBadge(data.jenis_presensi)}
                        </p>

                        <p><span class="font-medium">Tanggal:</span> 
                            ${data.tanggal}
                        </p>

                        <p><span class="font-medium">Waktu:</span> 
                            ${data.waktu_presensi}
                        </p>

                        <p><span class="font-medium">Jarak:</span> 
                            ${
                                data.jarak_meter
                                    ? Number(
                                          parseFloat(data.jarak_meter).toFixed(
                                              2,
                                          ),
                                      ) + " meter"
                                    : "-"
                            }
                        </p>

                        <p class="md:col-span-2">
                            <span class="font-medium">Koordinat:</span>
                            ${
                                data.latitude && data.longitude
                                    ? parseFloat(data.latitude).toFixed(8) +
                                      ", " +
                                      parseFloat(data.longitude).toFixed(8)
                                    : "-"
                            }
                        </p>

                    </div>

                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-600 mb-2">
                            Bukti Presensi
                        </p>

                        ${
                            data.foto_presensi
                                ? `<a href="/storage/${data.foto_presensi}" target="_blank">
                                        <img src="/storage/${data.foto_presensi}" 
                                            class="w-48 rounded border hover:opacity-80 transition">
                                </a>`
                                : data.bukti_izin
                                  ? `<a href="/storage/${data.bukti_izin}" target="_blank">
                                            <img src="/storage/${data.bukti_izin}" 
                                                class="w-48 rounded border hover:opacity-80 transition">
                                    </a>`
                                  : `<span class="text-sm text-gray-400">
                                            Tidak ada bukti presensi
                                    </span>`
                        }
                    </div>

                    <!-- GARIS PEMBATAS -->
                    <div class="border-t my-4"></div>

                    <!-- VALIDATION ACTION -->
                    <div class="space-y-4">

                        <!-- TERIMA -->
                        <form method="POST" action="/pembimbing/validasi-presensi/${data.id}/terima">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute("content")}">
                            <button type="submit"
                                class="px-5 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                Terima Presensi
                            </button>
                        </form>

                        <!-- TOLAK -->
                        <form method="POST" action="/pembimbing/validasi-presensi/${data.id}/tolak" class="space-y-2">

                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute("content")}">

                            <textarea name="alasan_penolakan"
                                rows="2"
                                placeholder="Tuliskan alasan penolakan presensi..."
                                class="w-full px-3 py-2 border rounded text-sm focus:ring focus:ring-red-200"
                                required></textarea>

                            <button type="submit"
                                class="px-5 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                                Tolak Presensi
                            </button>

                        </form>

                    </div>

                </div>
                `;

            cardContainer.insertAdjacentHTML("afterbegin", card);
            highlight("presensi-card-" + data.id);

            return;
        }

        /* =============================
           TABLE PAGE
        ============================= */

        const table = document.querySelector("table[data-realtime]");
        if (!table) return;

        const mode = table.getAttribute("data-realtime");
        const tbody = table.querySelector("tbody");
        if (!tbody) return;

        if (document.getElementById("presensi-row-" + data.id)) return;

        if (mode === "rekap-presensi") {
            renderRekapPresensi(tbody, data);
        }

        if (mode === "monitoring-presensi") {
            renderMonitoringPresensi(tbody, data);
        }

        if (mode === "riwayat-presensi") {
            renderRiwayatPresensi(tbody, data);
        }
    }

    /* =============================
       RENDER FUNCTIONS
    ============================= */

    function renderRekapPresensi(tbody, data) {
        const alasan =
            data.status_validasi === "ditolak" && data.alasan_penolakan
                ? `<div class="text-xs text-red-600 mt-1">
                Alasan: ${data.alasan_penolakan}
           </div>`
                : "";

        const row = `
        <tr id="presensi-row-${data.id}" class="border-b hover:bg-gray-50">

            <td class="px-4 py-3">
                ${data.tanggal}
            </td>

            <td class="px-4 py-3">
                ${data.nama_siswa}
            </td>

            <td class="px-4 py-3">
                ${data.nama_perusahaan ?? "-"}
            </td>

            <td class="px-4 py-3">
                ${jenisBadge(data.jenis_presensi)}
            </td>

            <td class="px-4 py-3">
                ${statusBadge(data.status_validasi)}
                ${alasan}
            </td>

            <td class="px-4 py-3">
                ${buktiLink(data)}
            </td>

        </tr>
    `;

        tbody.insertAdjacentHTML("afterbegin", row);
    }

    function renderMonitoringPresensi(tbody, data) {
        const lat = data.latitude ? parseFloat(data.latitude).toFixed(8) : "-";

        const lng = data.longitude
            ? parseFloat(data.longitude).toFixed(8)
            : "-";

        const jarak = data.jarak_meter
            ? `
            <div class="text-gray-800">
                Jarak: ${parseFloat(data.jarak_meter).toFixed(2)} m
            </div>
            <div class="text-gray-400">
                ${lat}, ${lng}
            </div>
        `
            : "-";

        let bukti = "-";

        if (data.foto_presensi) {
            bukti = `<a href="/storage/${data.foto_presensi}" 
                    target="_blank"
                    class="text-indigo-600 hover:underline">
                    Lihat Foto
                 </a>`;
        } else if (data.bukti_izin) {
            bukti = `<a href="/storage/${data.bukti_izin}" 
                    target="_blank"
                    class="text-indigo-600 hover:underline">
                    Lihat Bukti
                 </a>`;
        }

        const alasan =
            data.status_validasi === "ditolak" && data.alasan_penolakan
                ? `<div class="text-xs text-red-600 mt-1">
                Alasan: ${data.alasan_penolakan}
           </div>`
                : "";

        const row = `
        <tr id="presensi-row-${data.id}" class="hover:bg-gray-50 align-top">

            <!-- TANGGAL -->
            <td class="px-4 py-2">
                ${data.tanggal}
            </td>

            <!-- NAMA -->
            <td class="px-4 py-2 font-medium">
                ${data.nama_siswa}
            </td>

            <!-- JENIS -->
            <td class="px-4 py-2">
                ${jenisBadge(data.jenis_presensi)}
            </td>

            <!-- WAKTU -->
            <td class="px-4 py-2">
                ${data.waktu_presensi}
            </td>

            <!-- JARAK -->
            <td class="px-4 py-2 text-xs">
                ${jarak}
            </td>

            <!-- STATUS -->
            <td class="px-4 py-2">
                ${statusBadge(data.status_validasi)}
                ${alasan}
            </td>

            <!-- BUKTI -->
            <td class="px-4 py-2">
                ${bukti}
            </td>

        </tr>
    `;

        tbody.insertAdjacentHTML("afterbegin", row);
        highlight("presensi-row-" + data.id);
    }

    function renderRiwayatPresensi(tbody, data) {
        const lat = data.latitude ? parseFloat(data.latitude).toFixed(8) : null;

        const lng = data.longitude
            ? parseFloat(data.longitude).toFixed(8)
            : null;

        const lokasi =
            data.jenis_presensi === "hadir" && data.jarak_meter
                ? `
            <div class="text-xs text-gray-800">
                Jarak: ${parseFloat(data.jarak_meter).toFixed(2)} m
            </div>
            <div class="text-xs text-gray-400">
                ${lat}, ${lng}
            </div>
        `
                : "-";

        const waktu =
            data.jenis_presensi === "alpha" || !data.waktu_presensi
                ? "-"
                : data.waktu_presensi;

        const alasan =
            data.status_validasi === "ditolak" && data.alasan_penolakan
                ? `<div class="text-xs text-red-600 mt-1">
                Alasan: ${data.alasan_penolakan}
           </div>`
                : "";

        let bukti = "-";

        if (data.jenis_presensi === "hadir" && data.foto_presensi) {
            bukti = `<a href="/storage/${data.foto_presensi}"
                    target="_blank"
                    class="text-blue-600 hover:underline text-sm">
                    Lihat Foto
                 </a>`;
        } else if (data.jenis_presensi === "izin" && data.bukti_izin) {
            bukti = `<a href="/storage/${data.bukti_izin}"
                    target="_blank"
                    class="text-blue-600 hover:underline text-sm">
                    Lihat Bukti
                 </a>`;
        }

        const row = `
        <tr id="presensi-row-${data.id}" 
            class="border-b hover:bg-gray-50 align-top">

            <!-- TANGGAL -->
            <td class="px-4 py-3">
                ${data.tanggal}
            </td>

            <!-- JENIS -->
            <td class="px-4 py-3">
                ${jenisBadge(data.jenis_presensi)}
            </td>

            <!-- WAKTU -->
            <td class="px-4 py-3">
                ${waktu}
            </td>

            <!-- LOKASI -->
            <td class="px-4 py-3 text-xs">
                ${lokasi}
            </td>

            <!-- STATUS -->
            <td class="px-4 py-3">
                ${statusBadge(data.status_validasi)}
                ${alasan}
            </td>

            <!-- BUKTI -->
            <td class="px-4 py-3 text-xs">
                ${bukti}
            </td>

        </tr>
    `;

        tbody.insertAdjacentHTML("afterbegin", row);
        highlight("presensi-row-" + data.id);
    }

    /* =============================
       BADGE HELPERS
    ============================= */

    function jenisBadge(jenis) {
        let cls = "bg-gray-100 text-gray-700";
        let label = capitalize(jenis);

        if (jenis === "hadir") cls = "bg-green-100 text-green-700";
        if (jenis === "izin") cls = "bg-yellow-100 text-yellow-700";
        if (jenis === "libur") cls = "bg-slate-200 text-slate-700";
        if (jenis === "alpha") cls = "bg-red-100 text-red-700";

        return `<span class="px-2 py-1 text-xs rounded ${cls}">${label}</span>`;
    }

    function statusBadge(status) {
        let cls = "bg-gray-100 text-gray-600";
        let label = capitalize(status);

        if (status === "menunggu") {
            cls = "bg-blue-100 text-blue-700";
            label = "Menunggu";
        }

        if (status === "diterima") {
            cls = "bg-green-100 text-green-700";
            label = "Diterima";
        }

        if (status === "ditolak") {
            cls = "bg-red-100 text-red-700";
            label = "Ditolak";
        }

        return `<span class="px-2 py-1 text-xs rounded ${cls}">${label}</span>`;
    }

    function buktiLink(data) {
        if (data.foto_presensi) {
            return `<a href="/storage/${data.foto_presensi}" target="_blank" class="text-blue-600 hover:underline">Lihat Foto</a>`;
        }

        if (data.bukti_izin) {
            return `<a href="/storage/${data.bukti_izin}" target="_blank" class="text-blue-600 hover:underline">Lihat Bukti</a>`;
        }

        return "-";
    }

    function capitalize(str) {
        if (!str) return "";
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function highlight(id) {
        setTimeout(() => {
            const el = document.getElementById(id);
            if (el) el.classList.remove("bg-green-50");
        }, 3000);
    }
})();
