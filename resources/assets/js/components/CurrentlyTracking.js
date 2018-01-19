import React, { Component } from 'react';

class CurrentlyTracking extends React.Component {
    constructor(props) {
        super(props);
        this.state = {number: 0, length: 55, extraClass: 'currentlyTracking'};

        // setInterval(this.update, 500);
    }

    componentDidMount() {
        this.update();
        // this.timerID = setTimeout(()=>this.update(), 60000);
    }

    componentWillUnmount() {
      clearTimeout(this.timerID);
    }

    secondsToTime(secs){;
        let dur = moment.duration(secs, 'seconds');

        let time = (dur.days() ? dur.days() + ' days ' : '') + (dur.hours() ? dur.hours() + ' hours ' : '') +  dur.minutes() + " minutes " + (dur.hours() ? '' : dur.seconds() + "s");

        return time;
    }

    update() {
        var self = this;

        // do an ajax request to the api and get the updated currently tracking
        // Make a request for a user with a given ID
        axios.get('/api/currently-tracking')
          .then(function (response) {
              var task = response.data.data[0];
              var className = 'currentlyTracking';

              if ( task.task == 'stop' ) {
                  className += ' stop';
                  task.task = 'Not Tracking...'
              }

              self.setState({
               length: task['length'],
               time: self.secondsToTime(task['length']),
               task: task.task,
               notes: task.notes,
               classes: className
              });

              let timeout = (task['length'] < 60*60 ? 1000 : 20000)
              self.timerID = setTimeout(()=>self.update(), timeout);

          })
          .catch(function (error) {
            console.log(error);
            self.timerID = setTimeout(()=>self.update(), 5000);

          });
    }

    render() {
        return (
            <div className={this.state.classes}>
                <div className="currentlyTracking-task-container">
                <div className="currentlyTracking-label">
                    Currently Tracking
                </div>
                    <span className="currentlyTracking-task">
                        <strong>{this.state.task}</strong>
                    </span>

                    <strong className="currentlyTracking-time"> {this.state.time}</strong>
                </div>
                <div className="currentlyTracking-notes">{this.state.notes}</div>
            </div>
        )
    }
}

export default CurrentlyTracking;