import sys
import http.client
import json
import os


def write_stdout(s):
    # only eventlistener protocol messages may be sent to stdout
    sys.stdout.write(s)
    sys.stdout.flush()


def write_stderr(s):
    sys.stderr.write(s)
    sys.stderr.flush()


def main():
    while 1:
        # transition from ACKNOWLEDGED to READY
        write_stdout('READY\n')

        # read header line and print it to stderr
        line = sys.stdin.readline()
        write_stderr(line)

        # read event payload and print it to stderr
        headers = dict([x.split(':') for x in line.split()])
        data = sys.stdin.read(int(headers['len']))
        write_stderr(data)

        # send slack notification
        send_clack_notification()

        # transition from READY to ACKNOWLEDGED
        write_stdout('\nOK')


def send_clack_notification():
    os.environ['HOSTNAME'] = str(sys.argv[1])
    os.environ['APP_ENV'] = str(sys.argv[2])
    os.environ['SLACK_WEBHOOK'] = str(sys.argv[3])
    os.environ['SLACK_HOSTNAME'] = str(sys.argv[4])
    os.environ['APP_NAME'] = str(sys.argv[5])
    content = {
        "text": "The worker in pod `" + os.environ['HOSTNAME'] + "` in environment `" + os.environ[
            'APP_ENV'] + "` for app `" + os.environ['APP_NAME'] + "` has stopped"
    }
    conn = http.client.HTTPSConnection(os.environ['SLACK_HOSTNAME'], 443)
    conn.request(
        "POST",
        os.environ['SLACK_WEBHOOK'],
        json.dumps(content),
        {"Content-Type": "application/json"}
    )
    response = conn.getresponse()
    write_stdout(str(response.status)+'\n')
    write_stdout(str(response.reason)+'\n')
    write_stdout(str(response.read())+'\n')
    conn.close()


if __name__ == '__main__':
    main()
