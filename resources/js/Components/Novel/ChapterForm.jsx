import ReactQuill from 'react-quill';
import 'react-quill/dist/quill.snow.css';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import { useForm } from '@inertiajs/react';
import { useRef } from 'react';

export default function ChapterForm({ 
    chapterNumber, 
    novelId, 
    chapter, 
    onSubmit, 
    processing: submitting = false,
    isEdit = false
}) {
    // Renamed processing prop to submitting to avoid conflict with useForm's processing
    const quillRef = useRef();
    console.log("chapter number before", chapterNumber);
    // if(isEdit === false && chapterNumber > 1) {
    //     chapterNumber = chapterNumber + 1;
    // }
    // else if( isEdit === false && chapterNumber === 1) {
    //     chapterNumber = 1;
    // }
    console.log("chapter number now", chapterNumber);
    const { data, setData, errors, processing } = useForm({
        title: chapter?.title || "",
        content: chapter?.content || "",
        chapter_number: chapterNumber,
        novel_id: chapter?.novel_id || novelId,
    });

    const modules = {
        toolbar: [
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            [{ 'indent': '-1' }, { 'indent': '+1' }],
            [{ 'align': [] }],
            ['clean']
        ],
    };

    const formats = [
        'header',
        'bold', 'italic', 'underline', 'strike',
        'list', 'bullet',
        'indent',
        'align'
    ];

    const handleSubmit = (e) => {
        e.preventDefault();
        
        // Using the data from useForm for validation
        if (!data.title.trim()) {
            setData('title', data.title.trim());
            return;
        }
        
        if (!data.content.trim()) {
            setData('content', data.content.trim());
            return;
        }
        
        if (onSubmit) {
            onSubmit(data);
        }
    };

    // Hiển thị nhãn chapter dựa trên số chương
    const chapterLabel = isEdit 
        ? `Chapter ${chapterNumber}` 
        : `Chapter ${chapterNumber}`;

    return (
        <div className="max-w-4xl mx-auto py-6">
            <form onSubmit={handleSubmit}>
                <div className="mb-6">
                    <InputLabel htmlFor="title" value={chapterLabel} />
                    <TextInput
                        id="title"
                        type="text"
                        name="title"
                        value={data.title}
                        className="mt-1 block w-full"
                        onChange={(e) => setData('title', e.target.value)}
                    />
                    {errors.title && <InputError message={errors.title} className="mt-2" />}
                </div>

                <div className="mb-6">
                    <InputLabel htmlFor="content" value="Chapter Content" />
                    <div className="mt-1">
                        <ReactQuill
                            ref={quillRef}
                            theme="snow"
                            value={data.content}
                            onChange={(value) => setData('content', value)}
                            modules={modules}
                            formats={formats}
                            className="bg-white min-h-[300px]"
                        />
                    </div>
                    {errors.content && <InputError message={errors.content} className="mt-2" />}
                </div>

                <div className="flex items-center justify-end mt-8">
                    <PrimaryButton className="ml-4" disabled={processing || submitting}>
                        {processing || submitting ? 
                            isEdit ? 'Updating...' : 'In progress...':
                            isEdit ? 'Update chapter': 'Add new chapter'
                        }
                    </PrimaryButton>
                </div>
            </form>
        </div>
    );
}