import TextInput from "../TextInput";
import InputLabel from "../InputLabel";
import { useForm } from "@inertiajs/react";
import InputError from "../InputError";
import InputImage from "../InputImage";
import PrimaryButton from "../PrimaryButton";
import { useEffect, useState } from "react";

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
            console.log("data", data);
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