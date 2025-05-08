import Header from "../Header";
import NovelsColumn from "../NovelsColumn";

export default function GridNovels({title, novels}){
    const itemPerColumn = novels.length / 3;
    const col1Data = novels.slice(0, itemPerColumn);
    const col2Data = novels.slice(itemPerColumn, itemPerColumn * 2);
    const col3Data = novels.slice(itemPerColumn * 2);
    return (
        <div className="header" style={{ width: "100%"}}>
            <Header title = {title} is_expand = {true} />
            <div className="grid gap-x-6 3xl:grid-cols-4 2xl:grid-cols-3 lg:grid-cols-2 grid-cols-1">
                <NovelsColumn novels = {col1Data} />
                <NovelsColumn novels = {col2Data} />
                <NovelsColumn novels = {col3Data} />
            </div>
        </div>
    );
}
