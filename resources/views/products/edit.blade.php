<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form id="product-form" class="space-y-4">
                        @csrf
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title:</label>
                            <input type="text" name="title" id="title" required value="{{ $product->title }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <div id="title-error" class="text-red-500 text-sm"></div>
                        </div>

                        <div>
                            <label for="description"
                                class="block text-sm font-medium text-gray-700">Description:</label>
                            <textarea name="description" id="description" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ $product->description }}</textarea>
                            <div id="description-error" class="text-red-500 text-sm"></div>
                        </div>

                        <div>
                            <label for="main_image" class="block text-sm font-medium text-gray-700">Main Image:</label>
                            <input type="file" name="main_image" id="main_image"
                                class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                onchange="previewImage(event)" accept="image/*">
                            <img id="main_image_preview" class="mt-2 preview-image"
                                src="{{ $product->main_image ? asset('storage/' . $product->main_image) : '' }}" />
                            <div id="main_image-error" class="text-red-500 text-sm"></div>
                        </div>

                        <div>
                            <label for="variants" class="block text-sm font-medium text-gray-700">Variants:</label>
                            <div id="variants" class="space-y-2">
                                @foreach ($product->variants as $index => $variant)
                                <div class="flex space-x-2 items-center">
                                    <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                                    <input type="text" name="variants[{{ $index }}][size]" placeholder="Size"
                                        value="{{ $variant->size }}"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <input type="text" name="variants[{{ $index }}][color]" placeholder="Color"
                                        value="{{ $variant->color }}"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <button type="button" class="text-red-600 hover:text-red-800"
                                        onclick="removeVariant(this)">Remove</button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" onclick="addVariant()"
                                class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Add
                                Variant</button>
                        </div>

                        <div>
                            <button type="submit" id="sbmt"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        .preview-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
    </style>
    <!-- Include Toastr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const preview = document.getElementById('main_image_preview');
                preview.src = reader.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        function addVariant() {
            const variantsDiv = document.getElementById('variants');
            const index = variantsDiv.children.length;
            const newVariant = document.createElement('div');
            newVariant.classList.add('flex', 'space-x-2', 'items-center');
            newVariant.innerHTML = `
                <input type="hidden" name="variants[${index}][id]" value="">
                <input type="text" name="variants[${index}][size]" placeholder="Size" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <input type="text" name="variants[${index}][color]" placeholder="Color" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <button type="button" class="text-red-600 hover:text-red-800" onclick="removeVariant(this)">Remove</button>
            `;
            variantsDiv.appendChild(newVariant);
            updateRemoveButtons();
        }

        function removeVariant(button) {
            const variantsDiv = document.getElementById('variants');
            if (variantsDiv.children.length > 1) {
                button.parentElement.remove();
            }
            updateRemoveButtons();
        }

        function updateRemoveButtons() {
            const variantsDiv = document.getElementById('variants');
            const removeButtons = variantsDiv.querySelectorAll('button');
            if (variantsDiv.children.length === 1) {
                removeButtons.forEach(button => button.disabled = true);
            } else {
                removeButtons.forEach(button => button.disabled = false);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateRemoveButtons();
        });

        document.getElementById('product-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const submitButton = event.submitter;
            submitButton.disabled = true;
            
            fetch("{{ route('product.update', $product->id) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.errors) {
                    handleErrors(data.errors);
                } else {
                    submitButton.disabled = true;
                    handleSuccess(data.message);
                }
                submitButton.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                submitButton.disabled = false;
            });
        });

        function handleErrors(errors) {
            clearErrors();
            for (const field in errors) {
                const errorElement = document.getElementById(`${field}-error`);
                if (errorElement) {
                    errorElement.innerText = errors[field][0];
                }
            }
        }

        function clearErrors() {
            document.querySelectorAll('.text-red-500.text-sm').forEach(element => {
                element.innerText = '';
            });
        }

        function handleSuccess(message) {
            clearErrors();
            const submitButton = document.querySelector('button[type="submit"]');
            submitButton.style.display = 'none';
            Toastify({
                text: message,
                duration: 2000, // Display duration in milliseconds
                gravity: 'top', // Toast position
                position: 'right', // Toast position
                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)", // Set your own color
                offset: {
                    x: 50, // horizontal axis - can be a number or a string indicating unity
                    y: 60 // vertical axis - can be a number or a string indicating unity
                },
                close: true // Make it possible for a user to manually close the toast
            }).showToast();

            setTimeout(() => {
                window.location.href = "{{ route('product.index') }}"; // Redirect to index page
            }, 2000); // Adjust timeout as needed
        }
    </script>
</x-app-layout>