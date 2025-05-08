import ParentsLayout from "@/Layouts/ParentsLayout";
import NotAuth from "@/Components/Home/NotAuth";
import NovelForm from "@/Components/Novel/NovelForm";
import { useState } from "react";
import Header from "@/Components/Header";

export default function CreateProject({ auth, tags }) {

    return (
        <ParentsLayout>
            {!auth.user ? (
                <NotAuth />
            ) : (
                <div>
                    <Header title="Create Project" />
                    <NovelForm tags={tags} />
                </div>
            )}
        </ParentsLayout>
    );
}
