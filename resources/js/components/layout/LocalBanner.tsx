import { GlobeLock } from 'lucide-react';
import { useState } from 'react';

const LocalBanner = () => {
    const [show, setShow] = useState(true);

    if (!show) return null;
    return (
        <div className="fixed right-0 top-0 size-12 items-center flex justify-center bg-red-500/30 text-white">
            <GlobeLock className='size-3/5 text-white' />
            <button
                onClick={() => setShow(false)}
                type="button"
                className="absolute inset-0"
            />
        </div>
    );
};

export default LocalBanner;
