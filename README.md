# Alfred Time Tracker

Simple time tracking workflow for keeping track of tasks.

## Prerequisites
* PHP >= 7.0
* [Composer](https://getcomposer.org/) (used for keeping up to date)

## Installation

Download `bin/alfred-time-tracker.alfredworkflow` and open it with Alfred.

## Updating

Run `tt :update` to update the time tracker. This will update the code within the workflow, as well as any dependencies using Composer.


## Usage

Special commands start with a colon i.e. `tt :stop` or `tt :report`, everything else will get tracked as a task.

### Tracking a Task
To start tracking a task simply type: `tt <task-name>`. You should get a notification saying that the tracking has started.

### Stop Tracking
To stop tracking type `tt :stop`

### Adding a note to a task
Sometimes you might be tracking lots of the same thing, but want to add notes to a specific task. Typing `tt :note` will bring up a list of the last logs, use the arrow keys to choose which task you which to write a note on and write the note, or hit tab to select it and then write the note.

![](http://c.dayjo.me/1j1v092a0s0z/Screen%20Recording%202018-01-05%20at%2002.38%20pm.gif)


### Generate Report
Typing `tt :report` will give you two options, monthly or yearly report. Both will generate a report based on the logs for the current year / month, and open a markdown file with the report in.