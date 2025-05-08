                // <div className="max-w-4xl mx-auto p-6">
                //     <h1 className="genshin-font text-2xl font-bold mb-6">Create New Novel</h1>
                    
                //     <form onSubmit={handleSubmit} className="space-y-6">
                //         {/* Title Input */}
                //         <div>
                //             <label className="block text-sm font-medium text-gray-700 mb-2">
                //                 Title
                //             </label>
                //             <TextInput
                //                 name="title"
                //                 value={formData.title}
                //                 onChange={handleInputChange}
                //                 className="w-full"
                //                 required
                //             />
                //         </div>

                //         {/* Description Input */}
                //         <div>
                //             <label className="block text-sm font-medium text-gray-700 mb-2">
                //                 Description
                //             </label>
                //             <textarea
                //                 name="description"
                //                 value={formData.description}
                //                 onChange={handleInputChange}
                //                 rows="4"
                //                 className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                //                 required
                //             />
                //         </div>

                //         {/* Cover Image Upload */}
                //         <div>
                //             <label className="block text-sm font-medium text-gray-700 mb-2">
                //                 Cover Image
                //             </label>
                //             <div className="flex items-center space-x-4">
                //                 <input
                //                     type="file"
                //                     accept="image/*"
                //                     onChange={handleImageUpload}
                //                     className="hidden"
                //                     id="coverImage"
                //                     required
                //                 />
                //                 <label
                //                     htmlFor="coverImage"
                //                     className="px-4 py-2 bg-gray-100 rounded-md cursor-pointer hover:bg-gray-200"
                //                 >
                //                     Choose File
                //                 </label>
                //                 {previewImage && (
                //                     <img
                //                         src={previewImage}
                //                         alt="Preview"
                //                         className="h-20 w-20 object-cover rounded"
                //                     />
                //                 )}
                //             </div>
                //         </div>

                //         {/* Content File Upload */}
                //         <div>
                //             <label className="block text-sm font-medium text-gray-700 mb-2">
                //                 Content File
                //             </label>
                //             <input
                //                 type="file"
                //                 accept=".txt,.doc,.docx,.pdf"
                //                 onChange={handleContentUpload}
                //                 className="w-full"
                //                 required
                //             />
                //         </div>

                //         {/* Submit Button */}
                //         <div className="flex justify-end">
                //             <SecondaryButton type="submit">
                //                 Create Novel
                //             </SecondaryButton>
                //         </div>
                //     </form>
                // </div>
    // const [previewImage, setPreviewImage] = useState(null);
    // const handleImageUpload = (e) => {
    //     const file = e.target.files[0];
        
    //     if (file) {
    //         // Cập nhật state trong useForm
    //         setData("image", file);
            
    //         // Tạo URL tạm thời để hiển thị preview
    //         const objectUrl = URL.createObjectURL(file);
    //         setPreviewImage(objectUrl);
            
    //         // Ghi log để debug
    //         console.log("File selected:", {
    //             name: file.name,
    //             type: file.type,
    //             size: `${(file.size / 1024).toFixed(2)} KB`
    //         });
    //     }
    // };
    export default function NovelForm({ novel, tags, isEditing = false }) {
        const { data, setData, post, put, errors, processing } = useForm({
            title: novel?.title || "",
            description: novel?.description || "",
            tags: novel?.tags || [],
            image: null,
            status: novel?.status || "ongoing",
        });
    
        const [imagePreview, setImagePreview] = useState(novel?.image_url || null);
    
        const statusOptions = [
            { value: "ongoing", label: "Ongoing" },
            { value: "completed", label: "Completed" },
        ];
    
        useEffect(() => {
            if (isEditing && novel) {
                setData({
                    title: novel.title || "",
                    description: novel.description || "",
                    tags: Array.isArray(novel.tags)
                        ? novel.tags.map((tag) => (typeof tag === "object" ? tag.id : tag))
                        : [],
                    image: null,
                    status: novel.status || "ongoing",
                });
                setImagePreview(novel.image_url);
            }
        }, [novel, isEditing]);
    
        const toggleTag = (tagId) => {
            if (data.tags.includes(tagId)) {
                setData("tags", data.tags.filter((id) => id !== tagId));
            } else {
                setData("tags", [...data.tags, tagId]);
            }
        };
    
        const handleImageChange = (file) => {
            setData("image", file);
        };
    
        const submit = (e) => {
            e.preventDefault();
    
            const formData = new FormData();
            formData.append("title", data.title);
            formData.append("description", data.description);
            formData.append("status", data.status);
            data.tags.forEach((tagId, index) => {
                formData.append(`tags[${index}]`, tagId);
            });
            if (data.image) {
                formData.append("image", data.image);
            } else if (isEditing && novel?.image_url) {
                formData.append("image_url", novel.image_url); // Gửi ảnh cũ nếu không thay đổi
            }
    
            if (isEditing && novel?.id) {
                post(route("update-novel", novel.id), {
                    data: formData,
                    forceFormData: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        if (!imagePreview) {
                            setImagePreview(null);
                        }
                    },
                });
            } else {
                post(route("add-novel"), {
                    data: formData,
                    forceFormData: true,
                    onSuccess: () => {
                        setData({
                            title: "",
                            description: "",
                            tags: [],
                            image: null,
                            status: "ongoing",
                        });
                        setImagePreview(null);
                    },
                });
            }
        };
    
        return (
            <form
                onSubmit={submit}
                className="max-w-4xl mx-auto p-6 bg-white rounded shadow"
                encType="multipart/form-data"
            >
                {/* Các trường form giữ nguyên, chỉ bỏ input ẩn cho tags */}
                <div className="mb-6">
                    <InputLabel htmlFor="title" value="Title" />
                    <TextInput
                        id="title"
                        name="title"
                        value={data.title}
                        onChange={(e) => setData("title", e.target.value)}
                        className="w-full mt-1"
                        required
                    />
                    <InputError message={errors.title} className="mt-2" />
                </div>
    
                <div className="mb-6">
                    <InputLabel htmlFor="description" value="Description" />
                    <textarea
                        id="description"
                        name="description"
                        value={data.description}
                        onChange={(e) => setData("description", e.target.value)}
                        className="w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        rows="6"
                        required
                    />
                    <InputError message={errors.description} className="mt-2" />
                </div>
    
                <div className="mb-6">
                    <InputLabel htmlFor="tags-input" value="Choose tags" />
                    <div className="flex flex-wrap gap-2 mt-2">
                        {tags &&
                            Array.isArray(tags) &&
                            tags.map((tag) => (
                                <div
                                    key={tag.id}
                                    onClick={() => toggleTag(tag.id)}
                                    className={`cursor-pointer px-3 py-1 rounded-full text-sm font-medium transition-colors ${
                                        data.tags.includes(tag.id)
                                            ? "bg-genshin-green"
                                            : "bg-gray-200 text-gray-700 hover:bg-gray-300"
                                    }`}
                                >
                                    {tag.name}
                                </div>
                            ))}
                    </div>
                    <InputError message={errors.tags} className="mt-2" />
                </div>
    
                {isEditing ? (
                    <div className="mb-6">
                        <InputLabel htmlFor="status" value="Novel Status" />
                        <select
                            id="status"
                            name="status"
                            value={data.status}
                            onChange={(e) => setData("status", e.target.value)}
                            className="w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            {statusOptions.map((option) => (
                                <option key={option.value} value={option.value}>
                                    {option.label}
                                </option>
                            ))}
                        </select>
                        <p className="mt-1 text-sm text-gray-500">
                            This will inform readers about the current status of your novel
                        </p>
                        <InputError message={errors.status} className="mt-2" />
                    </div>
                ) : null}
    
                <div className="mb-6">
                    <InputLabel htmlFor="image" value="Cover image" />
                    <InputImage
                        id="image"
                        name="image"
                        onChange={handleImageChange}
                        defaultImage={imagePreview}
                        resetPreview={!data.image && !imagePreview}
                    />
                    {isEditing && (
                        <p className="mt-1 text-sm text-gray-500">
                            Leave empty to keep current image
                        </p>
                    )}
                    <InputError message={errors.image} className="mt-2" />
                </div>
    
                <div className="mb-6 flex justify-end">
                    <PrimaryButton type="submit" disabled={processing}>
                        {processing
                            ? isEditing
                                ? "Updating..."
                                : "Creating..."
                            : isEditing
                            ? "Update Novel"
                            : "Create Novel"}
                    </PrimaryButton>
                </div>
            </form>
        );
    }