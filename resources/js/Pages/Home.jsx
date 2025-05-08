import ParentsLayout from '@/Layouts/ParentsLayout';
import RowNovels from '@/Components/NovelsLayouts/RowNovels';
import TopNovels from '@/Components/Home/TopNovels';
import GridNovels from '@/Components/NovelsLayouts/GridNovels';
export default function Home({topNovels, latestChapters, randomNovels}) {
    return (
        <ParentsLayout>
                <TopNovels novels={topNovels} />
                <GridNovels title = "Lastest Update" novels={latestChapters} />
                <RowNovels title = "Recommend" novels={randomNovels} />
        </ParentsLayout>
    );
}