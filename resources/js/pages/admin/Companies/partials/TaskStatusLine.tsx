import { App } from '@/wayfinder/types';
import { FC } from 'react';

export const taskStatusMap = {
    '1': 'Выполнена',
    '2': 'Ожидает выполнения',
    '3': 'На проверке',
    '4': 'Требует доработки',
    '5': 'Просрочена',
};

const TaskStatusLine: FC<{
    tasks: App.Models.Task[];
    status: '1' | '2' | '3' | '4' | '5';
}> = ({ tasks, status }) => {
    const matchingTasks = tasks.filter((task) => task.status === status).length;

    if (matchingTasks === 0) return null;

    return (
        <span className='block text-xs'>
            {taskStatusMap[status]} : <strong>{matchingTasks}</strong>
        </span>
    );
};

export default TaskStatusLine;
