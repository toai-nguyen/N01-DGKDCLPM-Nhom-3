import Modal from "../Modal";
import TextInput from "../TextInput";
import InputError from "../InputError";
import InputLabel from "../InputLabel";
import SecondaryButton from "../SecondaryButton";
import DangerButton from "../DangerButton";
import { useForm } from "@inertiajs/react";
import { useRef, useState } from "react";

export default function DeleteNovelsForm({ novel }) {
    const [confirmingNovelDeletion, setConfirmingNovelDeletion] =
        useState(false);
    const passwordInput = useRef();
    const {
        data,
        setData,
        delete: destroy,
        processing,
        reset,
        errors,
        clearErrors,
    } = useForm({
        password: "",
    });
    const confirmNovelDeletion = () => {
        setConfirmingNovelDeletion(true);
    };
    const deleteNovel = (e) => {
        e.preventDefault();
        destroy(route("delete-novel", novel.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => passwordInput.current.focus(),
            onFinish: () => reset(),
        });
    };
    const closeModal = () => {
        setConfirmingNovelDeletion(false);
        clearErrors();
        reset();
    };
    return (
        <div>
            <DangerButton onClick={confirmNovelDeletion}>Delete</DangerButton>
            <Modal show={confirmingNovelDeletion} onClose={closeModal}>
                <form action="DELELE" onSubmit={deleteNovel} className="p-6">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Delete Novel
                    </h2>
                    <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Once your novel is deleted, all of its resources and
                        data will be permanently deleted. Before deleting your
                        novel, please download any data or information that you
                        wish to retain.
                    </p>
                    <div className="mt-6">
                        <InputLabel
                            htmlFor="password"
                            value="Password"
                            className="sr-only"
                        />
                        <TextInput
                            id="password"
                            type="password"
                            name="password"
                            ref={passwordInput}
                            value={data.password}
                            onChange={(e) =>
                                setData("password", e.target.value)
                            }
                            isFocused
                            placeholder="Enter your password"
                            className="mt-1 block w-3/4"
                        />
                        <InputError>{errors.password}</InputError>
                    </div>
                    <div className="mt-6 flex justify-end">
                        <SecondaryButton onClick={closeModal}>
                            Cancel
                        </SecondaryButton>

                        <DangerButton className="ms-3" disabled={processing}>
                            Delete Novel
                        </DangerButton>
                    </div>
                </form>
            </Modal>
        </div>
    );
}
