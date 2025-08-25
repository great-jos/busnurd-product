<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{
        products: @js($products),
        selectedProduct: {},
    }"
    x-on:product-created.window="products.push($event.detail)"
    x-on:product-deleted.window="products = products.filter(product => product.id !== $event.detail.id);"
    x-on:product-updated.window="products = products.map(product => product.id === $event.detail.id ? { ...product, ...$event.detail } : product);"
    >
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Products</h3>
                        <x-primary-button x-on:click.prevent="$dispatch('open-modal', { name: 'add-product' })">
                            Add Product
                        </x-primary-button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        <template x-for="product in products" :key="product.id">
                            <div class="bg-gray-100 p-4 rounded-lg shadow-sm grid">
                                <!-- Image container -->
                                <div class="w-full h-48 overflow-hidden rounded-md mb-4">
                                    <img :src="product.image ? '/storage/' + product.image : '/images/placeholder.png'" 
                                        :alt="product.name" 
                                        class="w-full h-full object-cover">
                                </div>

                                <!-- Content -->
                                <div class="flex justify-between">
                                    <span class="text-lg font-bold" x-text="product.name"></span>
                                    <span class="text-gray-600" x-text="`$${product.price}`"></span>
                                </div>

                                <!-- Actions -->

                                <div class="mt-4">
                                    <button
                                        x-on:click.prevent="$dispatch('open-modal', { name: 'view-product', product: product })"
                                        class="text-indigo-600 hover:text-indigo-900"
                                    >
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="add-product" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ __('Add New Product') }}
            </h2>
            <form x-data="{
                form: { name: '', price: '', image: null, description: '' },
                submitForm() {
                    const formData = new FormData();
                    formData.append('name', this.form.name);
                    formData.append('price', this.form.price);
                    formData.append('description', this.form.description);
                    if (this.form.image) {
                        formData.append('image', this.form.image);
                    }

                    fetch('{{ route('products.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: formData,
                    })
                    .then(async response => {
                        if (!response.ok) {
                            let err = await response.json();

                            // Dispatch toast with validation error
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: {
                                    title: 'Validation Error',
                                    message: err.message || 'Something went wrong',
                                    type: 'error'
                                }
                            }));

                            throw err;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: {
                                    title: 'Success',
                                    message: 'Product created successfully',
                                    type: 'success'
                                }
                            }));
                            this.$dispatch('product-created', data.product);
                            this.form = { name: '', price: '', image: null, description: '' };
                            this.$dispatch('close-modal', 'add-product');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                },
            }" @submit.prevent="submitForm">
                @csrf
                <div class="mb-4">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" type="text" class="mt-1 block w-full" x-model="form.name" required autofocus />
                </div>
                <div class="mb-4">
                    <x-input-label for="price" :value="__('Price')" />
                    <x-text-input id="price" type="number" step="0.01" class="mt-1 block w-full" x-model="form.price" required />
                </div>
                <div class="mb-4">
                    <x-input-label for="image" :value="__('Image')" />
                    <input id="image" type="file" class="mt-1 block w-full" @change="form.image = $event.target.files[0]" />
                </div>
                <div class="mb-4">
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea id="description" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" rows="4" x-model="form.description"></textarea>
                </div>
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                    <x-primary-button class="ml-3">
                        {{ __('Save Product') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="view-product" focusable>
        <div class="p-6" 
            x-data="{
                selectedProduct: {},
            }"
            x-on:modal-opened.window="
                if ($event.detail.name === 'view-product') {
                    selectedProduct = $event.detail.product;
                }
            "
        >
            <h2 class="text-lg font-medium text-gray-900 mb-4" x-text="selectedProduct.name"></h2>
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/2">
                    <img :src="selectedProduct.image ? `/storage/${selectedProduct.image}` : '/images/placeholder.png'" :alt="selectedProduct.name" class="w-full h-auto object-cover rounded-lg">
                </div>
                <div class="md:w-1/2 md:pl-6 mt-4 md:mt-0">
                    <p class="text-gray-600 mt-2" x-text="`$${selectedProduct.price}`"></p>
                    <p class="mt-4" x-text="selectedProduct.description"></p>
                    <div class="mt-6 flex space-x-4">
                        <a x-on:click.prevent="$dispatch('open-modal', { name: 'edit-product', product: selectedProduct })" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Edit
                        </a>
                        <form 
                            {{-- :action="'/products/' + selectedProduct.id" method="POST" --}}
                            x-data="{
                                form: { name: '', price: '', image: null, description: '' },
                                submitDeleteForm(product) {
                                    if(confirm('Are you sure you want to delete ' + product.name + '?')){
                                        fetch('{{ route('products.index') }}' + '/' + product.id, {
                                            method: 'DELETE',
                                             headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                                                'Accept': 'application/json',
                                            }
                                        })
                                        .then(async response => {
                                            if (!response.ok) {
                                                let err = await response.json();

                                                window.dispatchEvent(new CustomEvent('toast', {
                                                    detail: {
                                                        title: 'Validation Error',
                                                        message: err.message || 'Something went wrong',
                                                        type: 'error'
                                                    }
                                                }));

                                                throw err;
                                            }
                                            return response.json();
                                        })
                                        .then(data => {
                                            if (data.success) {
                                                window.dispatchEvent(new CustomEvent('toast', {
                                                    detail: {
                                                        title: 'Success',
                                                        message: data.message || 'Product deleted successfully',
                                                        type: 'success'
                                                    }
                                                }));
                                                $dispatch('product-deleted', product);
                                                this.$dispatch('close-modal', 'view-product');
                                            }
                                        })
                                        .catch(error => console.error('Error:', error));
                                    }
                                }
                            }"
                            @submit.prevent="submitDeleteForm(selectedProduct)"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-modal>

    <x-modal name="edit-product" focusable>
        <div class="p-6"
            x-data="{
                productToEdit: {},
                form: { name: '', price: '', image: null, description: '' },
                setForm(product) {
                    this.productToEdit = product;
                    this.form = {
                        name: product.name,
                        price: product.price,
                        image: null,
                        description: product.description,
                    };
                },
                resetForm() { console.log('resetting form');
                    this.form = JSON.parse(JSON.stringify({ name: '', price: '', image: null, description: '' }));
                },
                submitEditForm(productId) {
                    const formData = new FormData();
                    formData.append('_method', 'PATCH');
                    formData.append('name', this.form.name);
                    formData.append('price', this.form.price);
                    formData.append('description', this.form.description);
                    if (this.form.image) {
                        formData.append('image', this.form.image);
                    }

                    fetch('{{ route('products.index') }}' + '/' + productId, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: formData,
                    })
                    .then(async response => {
                        if (!response.ok) {
                            let err = await response.json();
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: {
                                    title: 'Error',
                                    message: err.message || 'Something went wrong',
                                    type: 'error'
                                }
                            }));
                            throw err;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: {
                                    title: 'Success',
                                    message: data.message || 'Product updated successfully',
                                    type: 'success'
                                }
                            }));
                            $dispatch('product-updated', data.product);
                            this.resetForm();
                            $dispatch('close');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            }"
            x-on:modal-opened.window="
                if ($event.detail.name === 'edit-product') {
                    setForm($event.detail.product)
                }
            "
        >
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ __('Edit Product') }}
            </h2>
            <form @submit.prevent="submitEditForm(productToEdit.id)">
                @csrf
                <div class="mb-4">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" type="text" class="mt-1 block w-full" x-model="form.name" required autofocus />
                </div>
                <div class="mb-4">
                    <x-input-label for="price" :value="__('Price')" />
                    <x-text-input id="price" type="number" step="0.01" class="mt-1 block w-full" x-model="form.price" required />
                </div>
                <div class="mb-4">
                    <x-input-label for="image" :value="__('Image')" />
                    <input id="image" type="file" class="mt-1 block w-full" @change="form.image = $event.target.files[0]" />
                </div>
                <div class="mb-4">
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea id="description" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" rows="4" x-model="form.description"></textarea>
                </div>
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                    <x-primary-button class="ml-3">
                        {{ __('Update Product') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>

</x-app-layout>