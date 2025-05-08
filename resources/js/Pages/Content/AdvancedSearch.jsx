import ParentsLayout from "@/Layouts/ParentsLayout";
import Header from "@/Components/Header";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";
import { useForm, router } from "@inertiajs/react";
import PrimaryButton from "@/Components/PrimaryButton";
import { useEffect } from "react";

export default function AdvancedSearch({ tags }) {
    // Log tags khi component được render để kiểm tra dữ liệu
    
    const { data, setData, post, errors, processing } = useForm({
        title: "",
        author: "",
        tags: [],
        status: "ongoing",
    });
    
    const statusOptions = [
        { value: "ongoing", label: "Ongoing" },
        { value: "completed", label: "Completed" },
    ];
    
    // Thêm useEffect để kiểm tra khi component mount

    const toggleTag = (tagId) => {
        if (data.tags.includes(tagId)) {
            setData(
                "tags",
                data.tags.filter((id) => id !== tagId)
            );
        } else {
            setData("tags", [...data.tags, tagId]);
        }
    };
    
    const submit = (e) => {
        console.log("submit", data);
        e.preventDefault();
        router.post(route("advanced-search-query"), {
            data,
        });
    };
    
    return (
        <ParentsLayout>
            <div>
                <Header title="Advanced Search" />
                <form
                    onSubmit={submit}
                    className="max-w-4xl mx-auto p-6 bg-white rounded shadow"
                    encType="multipart/form-data"
                >
                    {/* novel title */}
                    <div className="mb-6">
                        <InputLabel htmlFor="title" value="Title" />
                        <TextInput
                            id="title"
                            name="title"
                            value={data.title}
                            onChange={(e) => setData("title", e.target.value)}
                            className="w-full mt-1"
                        />
                        <InputError message={errors.title} className="mt-2" />
                    </div>
                    
                    {/* author name */}
                    <div className="mb-6">
                        <InputLabel htmlFor="name" value="Author name" />
                        <TextInput
                            id="name"
                            name="name"
                            value={data.author}
                            onChange={(e) => setData("author", e.target.value)}
                            className="w-full mt-1"
                        />
                        <InputError message={errors.author} className="mt-2" />
                    </div>
                    
                    <div className="mb-6">
                        <InputLabel htmlFor="tags-input" value="Choose tags" />
                        <div className="flex flex-wrap gap-2 mt-2">
                            {Array.isArray(tags) && tags.length > 0 ? (
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
                                        {tag.tag_name  || `Tag ${tag.id}`}
                                    </div>
                                ))
                            ) : (
                                <p className="text-gray-500">No tags available or still loading...</p>
                            )}
                        </div>
                        <InputError message={errors.tags} className="mt-2" />
                    </div>
                    
                    {/* status */}
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
                        <InputError message={errors.status} className="mt-2" />
                    </div>
                    
                    {/* Submit button */}
                    <div className="flex items-center justify-end mt-4">
                        <PrimaryButton type="submit" className="ml-4" disabled={processing}>
                            Search
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </ParentsLayout>
    );
}
