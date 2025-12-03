<div x-data="{
    show: false,
    actionUrl: '',
    saleNumber: '',

    open(data) {
        this.actionUrl = data.action;
        this.saleNumber = data.saleNumber;
        this.show = true;
    }
}" @open-cancel-modal.window="open($event.detail)" x-show="show"
    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
    x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
    class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true"
    style="display: none;">

    {{-- Backdrop --}}
    <div x-show="show" x-transition.opacity class="fixed inset-0 backdrop-blur bg-opacity-75 transition-opacity"
        @click="show = false"></div>

    {{-- Modal Content --}}
    <div class="flex items-end sm:items-center justify-center min-h-full p-4 text-center sm:p-0 rounded-xl">
        <div class="relative bg-white rounded-xl shadow-xl transform transition-all sm:my-8 sm:w-full sm:max-w-lg">

            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">

                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="bi bi-exclamation-triangle-fill text-red-600 text-xl"></i>
                    </div>

                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            Konfirmasi Pembatalan Transaksi
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Apakah Anda yakin ingin membatalkan transaksi ini? Tindakan ini tidak dapat dibatalkan.
                            </p>
                            <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="font-semibold text-red-700">Nomor Transaksi:</p>
                                <span class="text-xl font-extrabold text-red-900" x-text="saleNumber"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">

                {{-- Form untuk pembatalan (HTTP DELETE) --}}
                <form :action="actionUrl" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent 
                                shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white 
                                hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Ya, Batalkan Transaksi
                    </button>
                </form>

                <button type="button" @click="show = false"
                    class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 
                            shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 
                            hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
