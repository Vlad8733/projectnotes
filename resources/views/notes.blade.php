<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notes') }}
        </h2>
    </x-slot>

    <style>
        @keyframes gradientBackground {
            0% {
                background-color: #FF8C00;
            }
            25% {
                background-color: #FF4500;
            }
            100% {
                background-color: #FFA500;
            }
            
        }

        .container {
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: gradientBackground 5s infinite;
        }

        .title {
            font-size: 24px;
            color: #2d3748;
            margin-bottom: 15px;
        }

        .photo-list li {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .photo-list img {
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .photo-list .text-sm {
            color: #4a5568;
        }

        .photo-list .text-blue-500 {
            font-weight: 600;
        }

        .photo-list .text-red-500 {
            font-weight: 600;
        }

        .modal {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
        }

        .modal h3 {
            font-size: 20px;
            color: #2d3748;
        }

        .modal input[type="text"] {
            margin-top: 10px;
            margin-bottom: 10px;
        }
    </style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <x-application-logo class="block h-12 w-auto" />
                </div>

                <div>
                    <div class="container px-3 max-w-md mx-auto">
                        <!-- photo wrapper -->
                        <div class="bg-white rounded shadow px-4 py-4" x-data="app()">
                            <div class="title font-bold text-lg">Notes app</div>
                            <div class="mt-4">
                                <input type="file" accept="image/*" @change="handleFileUpload" x-ref="photoInput" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            </div>
                            <div class="mt-4">
                                <input type="text" placeholder="Enter a caption" class="rounded-sm shadow-sm px-4 py-2 border border-gray-200 w-full" x-model="photoCaption">
                            </div>
                            <button @click="addPhoto" class="mt-4 px-4 py-2 bg-blue-500 text-black rounded hover:bg-blue-600" style="z-index: 999;">Add Notes</button>

                            <!-- photo list -->
                            <ul class="photo-list mt-4">
                                <template x-for="photo in photos" :key="photo.id">
                                    <li class="flex flex-col mt-4" x-show="photo.caption !== '' && photo.url !== ''">
                                        <img :src="photo.url" alt="Photo" class="w-full rounded max-w-xs max-h-40 object-cover" >
                                        <div class="mt-2 text-sm font-semibold" x-text="photo.caption"></div>
                                            <div class="flex justify-between mt-2">
                                            <button class="text-blue-500" @click="editPhoto(photo.id)">Edit</button>
                                            <span>&nbsp;</span>
                                         <button class="text-red-500" @click="deletePhoto(photo.id)">Delete</button>
                                     </div>
                                    </li>
                                </template>
                            </ul>

                            <!-- Modal for photo editing -->
                            <div x-show="isEditing" class="fixed z-10 inset-0 overflow-y-auto">
                                <div class="flex items-center justify-center min-h-screen">
                                    <div class="fixed inset-0 transition-opacity" @click="closeModal()">
                                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                    </div>
                                    <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                        Edit Photo
                                                    </h3>
                                                    <div class="mt-2">
                                                        <img :src="currentPhoto.url" alt="Editing Photo" id="editingImage" class="w-full rounded">
                                                    </div>
                                                    <div class="mt-4">
                                                        <input type="text" placeholder="Edit caption" class="rounded-sm shadow-sm px-4 py-2 border border-gray-200 w-full" x-model="currentCaption">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button @click="saveChanges" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-500 font-medium text-gr   hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Save</button>
                                            <button @click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script src="https://unpkg.com/cropperjs"></script>
                    <link rel="stylesheet" href="https://unpkg.com/cropperjs/dist/cropper.css" />

                    <script>
                        function app() {
                            return {
                                photos: JSON.parse(localStorage.getItem('photos')) || [],
                                photoCaption: "",
                                photoId: (JSON.parse(localStorage.getItem('photos')) || []).length > 0 ? (JSON.parse(localStorage.getItem('photos')).slice(-1)[0].id + 1) : 1,
                                isEditing: false,
                                currentPhoto: null,
                                currentCaption: "",
                                cropper: null,

                                addPhoto() {
                                    const photoInput = this.$refs.photoInput;

                                    if (photoInput.files.length === 0 || this.photoCaption.trim() === "") {
                                        return;
                                    }

                                    const reader = new FileReader();
                                    reader.onload = () => {
                                        this.photos.push({
                                            id: this.photoId,
                                            url: reader.result,
                                            caption: this.photoCaption
                                        });

                                        this.photoId++;
                                        this.photoCaption = "";
                                        photoInput.value = "";

                                        localStorage.setItem('photos', JSON.stringify(this.photos));
                                    };
                                    reader.readAsDataURL(photoInput.files[0]);
                                },
                                deletePhoto(id) {
                                    this.photos = this.photos.filter((photo) => id !== photo.id);
                                    localStorage.setItem('photos', JSON.stringify(this.photos));
                                },
                                handleFileUpload(event) {
                                    this.$refs.photoInput = event.target;
                                },
                                editPhoto(id) {
                                    this.currentPhoto = this.photos.find(photo => photo.id === id);
                                    this.currentCaption = this.currentPhoto.caption;
                                    this.isEditing = true;

                                    this.$nextTick(() => {
                                        const image = document.getElementById('editingImage');
                                        this.cropper = new Cropper(image, {
                                            aspectRatio: 1,
                                            viewMode: 1
                                        });
                                    });
                                },
                                closeModal() {
                                    this.isEditing = false;
                                    if (this.cropper) {
                                        this.cropper.destroy();
                                        this.cropper = null;
                                    }
                                },
                                saveChanges() {
                                    this.currentPhoto.caption = this.currentCaption;

                                    if (this.cropper) {
                                        this.currentPhoto.url = this.cropper.getCroppedCanvas().toDataURL();
                                    }

                                    this.isEditing = false;
                                    this.cropper.destroy();
                                    this.cropper = null;

                                    localStorage.setItem('photos', JSON.stringify(this.photos));
                                }
                            };
                        }

                        document.addEventListener('alpine:init', () => {
                            Alpine.data('app', app);
                        });
                    </script>
                </div>

                <x-welcome />
            </div>
        </div>
    </div>
</x-app-layout>
