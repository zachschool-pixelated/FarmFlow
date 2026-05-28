@php
    $defaultContacts = [[
        'name' => '',
        'role' => '',
        'phone' => '',
        'email' => '',
        'notes' => '',
        'is_primary' => false,
    ]];

    $formContacts = old('contacts', $contactRows ?? $defaultContacts);

    if (empty($formContacts)) {
        $formContacts = $defaultContacts;
    }

    $supplierCode = $supplier->supplier_code ?? null;
    $isActive = old('is_active', $supplier->is_active ?? true);
    $isBlacklisted = old('is_blacklisted', $supplier->is_blacklisted ?? false);
    $blacklistReason = old('blacklist_reason', $supplier->blacklist_reason ?? '');
@endphp

<div x-data="{
    contacts: @js($formContacts),
    isBlacklisted: {{ $isBlacklisted ? 'true' : 'false' }},
    isActive: {{ $isActive ? 'true' : 'false' }},
    createAccount: {{ old('create_account') ? 'true' : 'false' }},
    imagePreview: '{{ $supplier->profile_picture ? asset('storage/' . $supplier->profile_picture) : '' }}',

    provinces: [],
    allCities: [],
    allBarangays: [],
    cities: [],
    barangays: [],
    
    selectedProvince: '{{ old('province', $supplier->province ?? '') }}',
    selectedCity: '{{ old('city', $supplier->city ?? '') }}',
    selectedBarangay: '{{ old('barangay', $supplier->barangay ?? '') }}',

    async init() {
        const [provRes, cityRes, brgyRes] = await Promise.all([
            fetch('/data/ph-json/province.json'),
            fetch('/data/ph-json/city.json'),
            fetch('/data/ph-json/barangay.json')
        ]);
        
        this.provinces = await provRes.json();
        this.allCities = await cityRes.json();
        this.allBarangays = await brgyRes.json();
        
        this.provinces.sort((a, b) => a.province_name.localeCompare(b.province_name));
        
        if (this.selectedProvince) this.updateCities(false);
        if (this.selectedCity) this.updateBarangays(false);
    },

    updateCities(reset = true) {
        if (reset) {
            this.selectedCity = '';
            this.selectedBarangay = '';
            this.barangays = [];
        }
        let prov = this.provinces.find(p => p.province_name === this.selectedProvince);
        if (prov) {
            this.cities = this.allCities.filter(c => c.province_code === prov.province_code).sort((a,b) => a.city_name.localeCompare(b.city_name));
        } else {
            this.cities = [];
        }
    },

    updateBarangays(reset = true) {
        if (reset) {
            this.selectedBarangay = '';
        }
        let city = this.allCities.find(c => c.city_name === this.selectedCity);
        if (city) {
            this.barangays = this.allBarangays.filter(b => b.city_code === city.city_code).sort((a,b) => a.brgy_name.localeCompare(b.brgy_name));
        } else {
            this.barangays = [];
        }
    },

    addContact() {
        this.contacts.push({ name: '', role: '', phone: '', email: '', notes: '', is_primary: false });
    },
    removeContact(index) {
        if (this.contacts.length > 1) {
            this.contacts.splice(index, 1);
        }
    },
    formatPhone(val) {
        if (!val) return '';
        let digits = val.toString().replace(/\D/g, '');
        if (digits.startsWith('63')) {
            digits = digits.substring(2);
        }
        if (digits.length === 0) return '+63';
        return '+63' + digits.substring(0, 10);
    }
}" x-effect="if (isBlacklisted) { isActive = false }">
    <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="grid items-start gap-6 lg:grid-cols-3">
        @csrf
        @if(! empty($formMethod) && $formMethod !== 'POST')
            @method($formMethod)
        @endif

        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 flex flex-col items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ __('Company Profile') }}</h3>
                
                <div class="relative mb-6 h-40 w-40 overflow-hidden rounded-full border-4 border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 shadow-inner">
                    <template x-if="imagePreview">
                        <img :src="imagePreview" alt="Profile Preview" class="h-full w-full object-cover">
                    </template>
                    <template x-if="!imagePreview">
                        <div class="flex h-full w-full items-center justify-center text-gray-400 dark:text-gray-500">
                            <svg class="h-16 w-16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </template>
                </div>

                <div class="w-full">
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg, image/png" class="hidden" 
                        @change="if ($event.target.files.length) { 
                                    const reader = new FileReader(); 
                                    reader.onload = (e) => imagePreview = e.target.result; 
                                    reader.readAsDataURL($event.target.files[0]); 
                                 }">
                    <label for="profile_picture" class="btn-secondary w-full text-center cursor-pointer block">
                        {{ __('Upload Logo') }}
                    </label>
                    <x-input-error :messages="$errors->get('profile_picture')" class="mt-2 text-center" />
                </div>
                <p class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">
                    {{ __('Allowed formats: JPEG, PNG, JPG.') }}<br>{{ __('Max size: 2MB.') }}
                </p>
            </div>

            @if(!$supplier->exists)
            <div class="rounded-2xl border border-farm-200 dark:border-farm-800 bg-farm-50/30 dark:bg-farm-900/20 p-5">
                <label class="flex items-start gap-3">
                    <input type="hidden" name="create_account" value="0">
                    <input type="checkbox" name="create_account" value="1" class="mt-1 h-4 w-4 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-farm-600 focus:ring-farm-500" x-model="createAccount">
                    <span>
                        <span class="block text-sm font-bold text-gray-900 dark:text-white">{{ __('Create user account') }}</span>
                        <span class="mt-1 block text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Grant supplier portal access.') }}</span>
                    </span>
                </label>

                <div x-show="createAccount" x-cloak class="mt-5 grid gap-4 border-t border-farm-100 dark:border-farm-800 pt-5" x-transition>
                    <div>
                        <x-input-label for="account_name" :value="__('Account Name')" />
                        <x-text-input id="account_name" name="account_name" class="mt-1 block w-full rounded-xl bg-white dark:bg-gray-800" :value="old('account_name')" placeholder="e.g. Juan Dela Cruz" x-bind:required="createAccount" />
                        <x-input-error :messages="$errors->get('account_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="account_email" :value="__('Login Email')" />
                        <x-text-input id="account_email" name="account_email" type="email" pattern=".*@.*" title="Please include an '@' in the email address." class="mt-1 block w-full rounded-xl bg-white dark:bg-gray-800" :value="old('account_email')" placeholder="contact@agricorp.com" x-bind:required="createAccount" />
                        <x-input-error :messages="$errors->get('account_email')" class="mt-2" />
                    </div>
                    <div x-data="{ show: false }">
                        <x-input-label for="account_password" :value="__('Temporary Password')" />
                        <div class="relative mt-1">
                            <x-text-input id="account_password" x-bind:type="show ? 'text' : 'password'" name="account_password" class="block w-full rounded-xl bg-white dark:bg-gray-800 pr-12" placeholder="Min 8 characters" x-bind:required="createAccount" />
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center justify-center px-4 text-gray-400 dark:text-gray-500 hover:text-farm-600 focus:outline-none" :title="show ? 'Hide password' : 'Show password'">
                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('account_password')" class="mt-2" />
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 lg:col-span-2 space-y-6">

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="supplier_code" :value="__('Supplier Code')" />
                @if($supplierCode)
                    <x-text-input id="supplier_code" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-900" :value="$supplierCode" readonly />
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('This code is auto-generated and cannot be edited.') }}</p>
                @else
                    <div class="mt-1 rounded-xl border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 px-4 py-3 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">
                        {{ __('A supplier code will be generated automatically after saving.') }}
                    </div>
                @endif
            </div>
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" name="name" class="mt-1 block w-full rounded-xl" :value="old('name', $supplier->name ?? '')" placeholder="e.g. AgriCorp Inc." required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <x-input-label for="contact_person" :value="__('Primary Contact')" />
                <x-text-input id="contact_person" name="contact_person" class="mt-1 block w-full rounded-xl" :value="old('contact_person', $supplier->contact_person ?? '')" placeholder="e.g. Juan Dela Cruz" />
                <x-input-error :messages="$errors->get('contact_person')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="phone" :value="__('Phone')" />
                <x-text-input id="phone" name="phone" class="mt-1 block w-full rounded-xl" :value="old('phone', $supplier->phone ?? '')" placeholder="+639123456789" x-on:input="$event.target.value = formatPhone($event.target.value)" />
            </div>
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" pattern=".*@.*" title="Please include an '@' in the email address." class="mt-1 block w-full rounded-xl" :value="old('email', $supplier->email ?? '')" placeholder="e.g. contact@agricorp.com" />
            </div>
        </div>

        <div class="col-span-full border border-gray-200 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-900/50 rounded-2xl p-5 mt-2">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600 pb-2 mb-4">{{ __('Address Details') }}</h4>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <x-input-label for="province" :value="__('State / Province')" />
                    <select id="province" name="province" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500 bg-white dark:bg-gray-800" x-model="selectedProvince" @change="updateCities()">
                        <option value="">{{ __('Select Province') }}</option>
                        <template x-for="prov in provinces" :key="prov.province_code">
                            <option :value="prov.province_name" x-text="prov.province_name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <x-input-label for="city" :value="__('City / Municipality')" />
                    <select id="city" name="city" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500 bg-white dark:bg-gray-800 disabled:bg-gray-100 dark:bg-gray-700 disabled:text-gray-500 dark:text-gray-400 dark:text-gray-500" x-model="selectedCity" @change="updateBarangays()" :disabled="!selectedProvince">
                        <option value="">{{ __('Select City') }}</option>
                        <template x-for="c in cities" :key="c.city_code">
                            <option :value="c.city_name" x-text="c.city_name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <x-input-label for="barangay" :value="__('Barangay')" />
                    <select id="barangay" name="barangay" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500 bg-white dark:bg-gray-800 disabled:bg-gray-100 dark:bg-gray-700 disabled:text-gray-500 dark:text-gray-400 dark:text-gray-500" x-model="selectedBarangay" :disabled="!selectedCity">
                        <option value="">{{ __('Select Barangay') }}</option>
                        <template x-for="b in barangays" :key="b.brgy_code">
                            <option :value="b.brgy_name" x-text="b.brgy_name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <x-input-label for="postal_code" :value="__('Postal Code')" />
                    <x-text-input id="postal_code" name="postal_code" class="mt-1 block w-full rounded-xl" :value="old('postal_code', $supplier->postal_code ?? '')" placeholder="e.g. 1000" />
                </div>
                <div class="md:col-span-2">
                    <x-input-label for="street_address" :value="__('Street Address')" />
                    <x-text-input id="street_address" name="street_address" class="mt-1 block w-full rounded-xl" :value="old('street_address', $supplier->street_address ?? '')" placeholder="e.g. 123 Farmville St., Bldg/House No." />
                </div>
            </div>
        </div>

        @if($supplier->exists && auth()->user()->isAdmin())
            <div class="grid gap-4 md:grid-cols-2">
                <label class="flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 p-4">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="mt-1 h-4 w-4 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-farm-600 focus:ring-farm-500" x-model="isActive" @checked($isActive) :disabled="isBlacklisted">
                    <span>
                        <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{ __('Active Status') }}</span>
                        <span class="mt-1 block text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Enable this supplier for normal operations.') }}</span>
                    </span>
                </label>
                <label class="flex items-start gap-3 rounded-2xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 p-4">
                    <input type="hidden" name="is_blacklisted" value="0">
                    <input type="checkbox" name="is_blacklisted" value="1" class="mt-1 h-4 w-4 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-red-600 focus:ring-red-500" x-model="isBlacklisted" @checked($isBlacklisted)>
                    <span>
                        <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{ __('Blacklist Supplier') }}</span>
                        <span class="mt-1 block text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Stop new requests and flag the company profile.') }}</span>
                    </span>
                </label>
            </div>

            <div x-show="isBlacklisted" x-cloak>
                <x-input-label for="blacklist_reason" :value="__('Blacklist Reason')" />
                <textarea id="blacklist_reason" name="blacklist_reason" rows="3" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500 placeholder-gray-400" placeholder="e.g. Consistent late deliveries...">{{ $blacklistReason }}</textarea>
                <x-input-error :messages="$errors->get('blacklist_reason')" class="mt-2" />
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Additional Contacts') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Add more than one contact person for the same supplier.') }}</p>
                </div>
                <button type="button" class="btn-secondary" @click="addContact()">{{ __('Add Contact') }}</button>
            </div>

            <div class="mt-4 space-y-4">
                <template x-for="(contact, index) in contacts" :key="index">
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 p-4 shadow-sm dark:shadow-gray-900/50">
                        <div class="flex items-center justify-between gap-3">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Contact') }} <span x-text="index + 1"></span></h4>
                            <button type="button" class="text-sm font-medium text-red-600 hover:text-red-700 disabled:cursor-not-allowed disabled:opacity-40" @click="removeContact(index)" :disabled="contacts.length === 1">{{ __('Remove') }}</button>
                        </div>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <x-input-label :value="__('Name')" />
                                <x-text-input x-bind:name="'contacts[' + index + '][name]'" x-model="contact.name" class="mt-1 block w-full rounded-xl" placeholder="e.g. Maria Santos" />
                            </div>
                            <div>
                                <x-input-label :value="__('Role / Title')" />
                                <x-text-input x-bind:name="'contacts[' + index + '][role]'" x-model="contact.role" class="mt-1 block w-full rounded-xl" placeholder="e.g. Sales Manager" />
                            </div>
                            <div>
                                <x-input-label :value="__('Phone')" />
                                <x-text-input x-bind:name="'contacts[' + index + '][phone]'" x-model="contact.phone" class="mt-1 block w-full rounded-xl" placeholder="+639123456789" x-on:input="contact.phone = formatPhone($event.target.value)" />
                            </div>
                            <div>
                                <x-input-label :value="__('Email')" />
                                <x-text-input x-bind:name="'contacts[' + index + '][email]'" x-model="contact.email" type="email" pattern=".*@.*" title="Please include an '@' in the email address." class="mt-1 block w-full rounded-xl" placeholder="e.g. maria@agricorp.com" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label :value="__('Notes')" />
                                <textarea x-bind:name="'contacts[' + index + '][notes]'" x-model="contact.notes" rows="3" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500 placeholder-gray-400" placeholder="e.g. Available during weekdays..."></textarea>
                            </div>
                            <label class="inline-flex items-center gap-2 md:col-span-2">
                                <input type="checkbox" x-bind:name="'contacts[' + index + '][is_primary]'" value="1" x-model="contact.is_primary" class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-farm-600 focus:ring-farm-500">
                                <span class="text-sm text-gray-700 dark:text-gray-200">{{ __('Mark as primary additional contact') }}</span>
                            </label>
                        </div>
                    </div>
                </template>
            </div>
        </div>


            <div class="flex items-center gap-3 pt-2">
                <x-primary-button>{{ $submitLabel }}</x-primary-button>
                <a href="{{ $cancelRoute ?? route('suppliers.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
            </div>
        </div>
    </form>
</div>