Someone sent a message through the website.

Name: {!! $contact->name !!}
Email: {!! $contact->email !!}
Phone: {!! $contact->phone ?: '(not given)' !!}
About: {!! __('site.contact.topics.'.$contact->topic, [], 'en') !!}

Message:
{!! $contact->message !!}

---
You can reply to this email directly. You can also read all messages at {!! route('admin.messages') !!}
