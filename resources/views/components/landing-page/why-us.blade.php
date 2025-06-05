<section class="bg-white">
    <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
        <div class="max-w-screen-xl text-center mb-8 lg:mb-16">
            <h2 class="mb-4 text-3xl tracking-tight font-extrabold text-emerald-700">
                Kenapa Harus Kami?
            </h2>
        </div>

        <!-- Kolom 1: Text kiri, Gambar kanan -->
        <div class="grid md:grid-cols-2 items-center gap-8">
            <!-- Text -->
            <div>
                <h2 class="text-3xl font-bold text-emerald-700 mb-4">Nggak Sembarangan Sikat!</h2>
                <p class="text-gray-600 text-justify">
                    Kami tahu rasanya punya sepatu kesayangan — yang dibeli dari hasil nabung, yang nemenin tiap langkah
                    penting, atau yang punya cerita sendiri. Makanya, kami nggak asal bersihin. Kami rawat sepatu kamu
                    seperti kami ngerawat sepatu kami sendiri: dengan sabun khusus, sikat pilihan, dan perhatian di
                    setiap detail. Karena buat kami, kebersihan itu bukan cuma soal tampilan, tapi juga menjaga nilai
                    dan kenangan di baliknya.
                </p>
            </div>
            <!-- Gambar -->
            <div>
                <img src="{{ asset('landing-page/undraw_love-it_8pc0.svg') }}" alt="Ilustrasi 1" class="w-full h-100">
            </div>
        </div>

        <!-- Kolom 2: Gambar kiri, Text kanan -->
        <div class="grid md:grid-cols-2 items-center gap-8">
            <!-- Gambar -->
            <div class="order-1 md:order-none">
                <img src="{{ asset('landing-page/undraw_order-delivered_puaw.svg') }}" alt="Ilustrasi 2"
                    class="w-full h-100">
            </div>
            <!-- Text -->
            <div>
                <h2 class="text-3xl font-bold text-emerald-700 mb-4">Nggak Perlu Ribet, Kami yang Datang!</h2>
                <p class="text-gray-600 text-justify">
                    Lagi sibuk atau mager keluar? Tenang. Kamu tinggal pesan, kami yang jemput sepatumu, bersihin sampai
                    kinclong, terus dianter balik tanpa drama. Hemat waktu, gak ganggu jadwal, dan pastinya bikin hidup
                    kamu lebih gampang.
                </p>
            </div>
        </div>

    </div>
</section>
