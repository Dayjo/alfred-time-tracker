# Alfred Time Tracker

Simple time tracking workflow for keeping track of tasks.

## Prerequisites
* Alfred 3 (though this could easily be converted for Alfred 2)
* PHP >= 7.0
* Git (used for keeping up to date)
* [Composer](https://getcomposer.org/) (used for keeping up to date)

## Installation

Download `bin/alfred-time-tracker.alfredworkflow` and open it with Alfred.

## Updating

It's recommended that you run the update after installation as the binary may not have the exact latest codebase.

Run `tt :update` to update the time tracker. This will update the code within the workflow, as well as any dependencies using Composer.

## Usage

Special commands start with a colon i.e. `tt :config`, `tt :stop` or `tt :report`, everything else will get tracked as a task.

---

### Configuring

You can set config variables with `tt :config`, it should bring up a list of possible config variables to change, you can either type out the name of the config var, or hit tab on the list item and it should pre-fill, then simply type the new value.

Alfred should display the current value of the config var in brackets.

![](http://c.dayjo.me/0D0Y0v3V0F0u/Image%202018-01-09%20at%2011.27.04%20am.png)

#### dayEnds
Setting the `dayEnds` config to a time, will make the report calculate the last task of the day up until the specified time. For instance, if I set dayEnds to `18:00`, and my last task was started at `16:00`, if I forget to stop the task at the end of the day, it will calculate 2 hours of work for the report.

Turning this off (`tt :config dayEnds false`) will mean that if you do not make sure that you stop your last task before you leave, it will not be able to calculate the number of hours for that last task.

---

### Open Workflow Directory
If you need to review your work logs, the config, the tasks list etc, you can easily open the workflow folder using;

`tt :open`

---

### Tracking a Task
To start tracking a task simply type: `tt <task-name>`. You should get a notification saying that the tracking has started.

---

### Stop Tracking
To stop tracking type `tt :stop`.

__Note;__ you don't need to stop tracking between tasks, you can track one, then start tracking another. You can only track one task at a time.

---

### Adding a note to a task
Sometimes you might be tracking lots of the same thing, but want to add notes to a specific task. Typing `tt :note` will bring up a list of the last logs, use the arrow keys to choose which task you which to write a note on and write the note, or hit tab to select it and then write the note.

![](http://c.dayjo.me/1j1v092a0s0z/Screen%20Recording%202018-01-05%20at%2002.38%20pm.gif)

---

### Generate Report
Typing `tt :report` will give you two options, monthly or yearly report. Both will generate a report based on the logs for the current year / month, and open a markdown file with the report in.