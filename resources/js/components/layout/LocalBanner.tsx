import { useState } from 'react';

const LocalBanner = () => {
    const [show, setShow] = useState(true);

    if (!show) return null;
    return (
        <div className="fixed inset-x-0 top-0 z-200 flex justify-center bg-red-500/30 p-4 text-white">
            Local development
            <button
                onClick={() => setShow(false)}
                type="button"
                className="absolute inset-0"
            />
        </div>
    );
};

export default LocalBanner;
