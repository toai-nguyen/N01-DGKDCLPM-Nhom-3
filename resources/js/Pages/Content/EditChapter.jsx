import ParentsLayout from "@/Layouts/ParentsLayout";
import ChapterForm from "@/Components/Novel/ChapterForm";
import Header from '@/Components/Header';
import { router } from '@inertiajs/react';
import { useState } from 'react';
export default function EditChapter({ chapter, novelId }) {
    const { chapter_number } = chapter;
    console.log("chapter", chapter_number);
    const [processing, setProcessing] = useState(false);
    const handleSubmit = (data) => {
        setProcessing(true);
        // send data to server
        const updateData = {
            title: data.title,
            content: data.content,
        };
        console.log("collection", data);
        router.post(route('chapter.update',{novel_id: novelId, chapter_id : chapter.id}), updateData, {
            onFinish: () => setProcessing(false)
        });
        
    };
    return (
        <ParentsLayout>
            <div className="">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="text-gray-900">
                            <Header title={`Edit: Chapter ${chapter_number}`} />
                            <ChapterForm 
                                chapter={chapter}
                                chapterNumber={chapter_number}
                                novelId={novelId} 
                                isEdit={true}
                                onSubmit={handleSubmit}
                                processing={processing}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </ParentsLayout>
    )
}
