import { useState } from 'react';
import ParentsLayout from "@/Layouts/ParentsLayout";
import ChapterForm from "@/Components/Novel/ChapterForm";
import Header from '@/Components/Header';
import { router } from '@inertiajs/react';

export default function CreateChapter({ chapterNumber, novelId }) {
    const [processing, setProcessing] = useState(false);
    // console.log("chapter number" ,chapterNumber)
    const handleSubmit = (data) => {
        setProcessing(true);
        console.log("Submitting chapter:", data);
        
        // send data to server
        router.post(route('chapter.store', novelId), data, {
            onFinish: () => setProcessing(false)
        });
    };

    // Tính toán tiêu đề cho trang tạo chapter
    if(chapterNumber > 1) {
        chapterNumber = chapterNumber + 1;
    }
    else if(chapterNumber === 1) {
        chapterNumber = 1;
    }
    const pageTitle = chapterNumber === 0 
        ? "Create First Chapter" 
        : `Create Chapter ${chapterNumber}`;

    return (
        <ParentsLayout>
            <div className="">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="text-gray-900">
                            <Header title={pageTitle} />
                            <ChapterForm 
                                chapterNumber={chapterNumber} 
                                novelId={novelId}
                                onSubmit={handleSubmit} 
                                processing={processing} 
                            />
                        </div>
                    </div>
                </div>
            </div>
        </ParentsLayout>
    );
}