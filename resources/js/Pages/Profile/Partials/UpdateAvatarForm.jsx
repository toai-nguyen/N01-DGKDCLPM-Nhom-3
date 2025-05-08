import { useRef, useState } from 'react';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import { useForm } from '@inertiajs/react';
import { Transition } from '@headlessui/react';

export default function UpdateAvatarForm({ className = '' }) {
    const fileInput = useRef();
    const [previewUrl, setPreviewUrl] = useState(null);
    
    const { data, setData, post, errors, processing, recentlySuccessful } = useForm({
        avatar: null,
    });

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        setData('avatar', file);
        
        // Tạo preview hình ảnh
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => setPreviewUrl(e.target.result);
            reader.readAsDataURL(file);
        }
    };

    const submit = (e) => {
        e.preventDefault();
        
        // Tạo FormData object để upload file
        const formData = new FormData();
        formData.append('avatar', data.avatar);
        console.log("image before submit: ", data.avatar);
        
        post(route('profile.update-avatar'), {
            data: formData,  // Gửi formData thay vì object thông thường
            forceFormData: true,  // Đảm bảo dữ liệu được gửi dưới dạng FormData
            preserveScroll: true,
            onSuccess: () => {
                fileInput.current.value = '';
                // Có thể thêm xử lý để reset preview
                setPreviewUrl(null);
            },
        });
    };

    return (
        <section className={className}>
            <header>
                <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">Profile Picture</h2>
                <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Update your profile picture.
                </p>
            </header>

            <form onSubmit={submit} className="mt-6 space-y-6">
                <div>
                    <InputLabel htmlFor="avatar" value="Avatar" />
                    
                    <input
                        ref={fileInput}
                        type="file"
                        id="avatar"
                        className="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-300"
                        onChange={handleFileChange}
                        accept="image/*"
                    />

                    <InputError className="mt-2" message={errors.avatar} />
                </div>

                {previewUrl && (
                    <div className="mt-4">
                        <p className="text-sm text-gray-600 dark:text-gray-400 mb-2">Preview:</p>
                        <img 
                            src={previewUrl} 
                            alt="Avatar preview" 
                            className="rounded-full w-24 h-24 object-cover border-2 border-gray-200"
                        />
                    </div>
                )}

                <div className="flex items-center gap-4">
                    <PrimaryButton disabled={processing}>Lưu</PrimaryButton>

                    <Transition
                        show={recentlySuccessful}
                        enter="transition ease-in-out"
                        enterFrom="opacity-0"
                        leave="transition ease-in-out"
                        leaveTo="opacity-0"
                    >
                        <p className="text-sm text-gray-600 dark:text-gray-400">Đã lưu.</p>
                    </Transition>
                </div>
            </form>
        </section>
    );
}